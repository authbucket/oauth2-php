<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Pantarei\OAuth2\Controller\TokenController;
use Pantarei\OAuth2\Provider\OAuth2ServiceProvider;
use Pantarei\OAuth2\Tests\Entity\ModelManagerFactory;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

/**
 * Extend Silex\WebTestCase for test case require database and web interface
 * setup.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class WebTestCase extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $app->register(new DoctrineServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new OAuth2ServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new TwigServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());

        $app['db.options'] = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $app['session.test'] = true;

        $app['twig.path'] = array(
            __DIR__ . '/views',
        );

        // Return an instance of Doctrine ORM entity manager.
        $app['pantarei_oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $event_manager = $app['dbs.event_manager']['default'];

            $config = Setup::createConfiguration(false);
            $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__ . '/Entity'));
            $config->setMetadataDriverImpl($driver);

            return EntityManager::create($conn, $config, $event_manager);
        });

        // Fake lib dev, simply use plain text encoder.
        $app['security.encoder.digest'] = $app->share(function ($app) {
            return new PlaintextPasswordEncoder();
        });

        // Add model managers from ORM.
        $app['pantarei_oauth2.model'] = array(
            'access_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\AccessToken',
            'authorize' => 'Pantarei\\OAuth2\\Tests\\Entity\\Authorize',
            'client' => 'Pantarei\\OAuth2\\Tests\\Entity\\Client',
            'code' => 'Pantarei\\OAuth2\\Tests\\Entity\\Code',
            'refresh_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\RefreshToken',
            'scope' => 'Pantarei\\OAuth2\\Tests\\Entity\\Scope',
        );
        $app['pantarei_oauth2.model_manager.factory'] = $app->share(function($app) {
            return new ModelManagerFactory($app['pantarei_oauth2.orm'], $app['pantarei_oauth2.model']);
        });

        // We simply reuse the user provider that already created for
        // authorize firewall here.
        $app['pantarei_oauth2.token_controller'] = $app->share(function () use ($app) {
            return new TokenController(
                $app['security'],
                $app['security.user_checker'],
                $app['security.encoder_factory'],
                $app['pantarei_oauth2.model_manager.factory'],
                $app['pantarei_oauth2.grant_handler.factory'],
                $app['pantarei_oauth2.token_handler.factory'],
                $app['security.user_provider.default']
            );
        });

        $app['security.firewalls'] = array(
            'resource' => array(
                'pattern' => '^/oauth2/resource',
                'oauth2_resource' => true,
                'stateless' => true,
            ),
            'token' => array(
                'pattern' => '^/oauth2/token',
                'oauth2_token' => true,
            ),
            'default' => array(
                'pattern' => '^/',
                'form' => array(
                    'login_path' => '/login',
                    'check_path' => '/login_check',
                ),
                'http' => true,
                'anonymous' => true,
                'users' => array(
                    'demousername1' => array('ROLE_USER', 'demopassword1'),
                    'demousername2' => array('ROLE_USER', 'demopassword2'),
                    'demousername3' => array('ROLE_USER', 'demopassword3'),
                ),
            ),
        );

        // Resource endpoint.
        $app->match('/oauth2/resource/username', function (Request $request, Application $app) {
            return $app['pantarei_oauth2.resource_controller']->usernameAction($request);
        });

        // Token endpoint.
        $app->post('/oauth2/token', function (Request $request, Application $app) {
            return $app['pantarei_oauth2.token_controller']->tokenAction($request);
        });

        // Authorization endpoint.
        $app->get('/oauth2/authorize', function (Request $request, Application $app) {
            return $app['pantarei_oauth2.authorize_controller']->authorizeAction($request);
        });

        // Form login.
        $app->get('/login', function (Request $request) use ($app) {
            return $app['twig']->render('login.html.twig', array(
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        });

        return $app;
    }

    public function setUp()
    {
        // Initialize with parent's setUp().
        parent::setUp();

        // Add tables and sample data.
        $this->createSchema();
        $this->addSampleData();
    }

    private function createSchema()
    {
        $em = $this->app['pantarei_oauth2.orm'];
        $modelManagerFactory = $this->app['pantarei_oauth2.model_manager.factory'];

        // Generate testing database schema.
        $classes = array(
            $em->getClassMetadata($modelManagerFactory->getModelManager('access_token')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('authorize')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('client')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('code')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('refresh_token')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('scope')->getClassName()),
        );

        PersistentObject::setObjectManager($em);
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
    }

    private function addSampleData()
    {
        $modelManagerFactory = $this->app['pantarei_oauth2.model_manager.factory'];

        // Add demo access token.
        $modelManager = $modelManagerFactory->getModelManager('access_token');
        $model = $modelManager->createAccessToken()
            ->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setExpires(new \DateTime('+1 hours'))
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateAccessToken($model);

        // Add demo authorizes.
        $modelManager = $modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->createAuthorize()
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateAuthorize($model);
        $model = $modelManager->createAuthorize()
            ->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $modelManager->updateAuthorize($model);
        $model = $modelManager->createAuthorize()
            ->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateAuthorize($model);
        $model = $modelManager->createAuthorize()
            ->setClientId('http://democlient1.com/')
            ->setUsername('')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateAuthorize($model);

        // Add demo clients.
        $modelManager =  $modelManagerFactory->getModelManager('client');
        $model = $modelManager->createClient()
            ->setClientId('http://democlient1.com/')
            ->setClientSecret('demosecret1')
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $modelManager->updateClient($model);
        $model = $modelManager->createClient()
            ->setClientId('http://democlient2.com/')
            ->setClientSecret('demosecret2')
            ->setRedirectUri('http://democlient2.com/redirect_uri');
        $modelManager->updateClient($model);
        $model = $modelManager->createClient()
            ->setClientId('http://democlient3.com/')
            ->setClientSecret('demosecret3')
            ->setRedirectUri('http://democlient3.com/redirect_uri');
        $modelManager->updateClient($model);
        $model = $modelManager->createClient()
            ->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('');
        $modelManager->updateClient($model);

        // Add demo code.
        $modelManager = $modelManagerFactory->getModelManager('code');
        $model = $modelManager->createCode()
            ->setCode('f0c68d250bcc729eb780a235371a9a55')
            ->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setRedirectUri('http://democlient2.com/redirect_uri')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $modelManager->updateCode($model);
        $model = $modelManager->createCode()
            ->setCode('1e5aa97ddaf4b0228dfb4223010d4417')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setRedirectUri('http://democlient1.com/redirect_uri')
            ->setExpires(new \DateTime('-10 minutes'))
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateCode($model);
        $model = $modelManager->createCode()
            ->setCode('08fb55e26c84f8cb060b7803bc177af8')
            ->setClientId('http://democlient4.com/')
            ->setUsername('demousername4')
            ->setRedirectUri('http://democlient4.com/redirect_uri')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateCode($model);

        // Add demo refresh token.
        $modelManager = $modelManagerFactory->getModelManager('refresh_token');
        $model = $modelManager->createRefreshToken()
            ->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setExpires(new \DateTime('+1 days'))
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateRefreshToken($model);
        $model = $modelManager->createRefreshToken()
            ->setRefreshToken('5ff43cbc27b54202c6fd8bb9c2a308ce')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setExpires(new \DateTime('-1 days'))
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateRefreshToken($model);

        // Add demo scopes.
        $modelManager = $modelManagerFactory->getModelManager('scope');
        $model = $modelManager->createScope()
            ->setScope('demoscope1');
        $modelManager->updateScope($model);
        $model = $modelManager->createScope()
            ->setScope('demoscope2');
        $modelManager->updateScope($model);
        $model = $modelManager->createScope()
            ->setScope('demoscope3');
        $modelManager->updateScope($model);
    }
}
