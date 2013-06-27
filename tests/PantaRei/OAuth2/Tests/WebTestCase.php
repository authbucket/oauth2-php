<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests;

use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PantaRei\OAuth2\Provider\OAuth2ServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $app['session'] = true;
        $app['exception_handler']->disable();

        $app->register(new DoctrineServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new OAuth2ServiceProvider());

        $app['db.options'] = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        // Return an instance of Doctrine ORM entity manager.
        $app['oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/Model'), true);
            $event_manager = $app['dbs.event_manager']['default'];
            return EntityManager::create($conn, $config, $event_manager);
        });

        // Add model managers from ORM.
        $models = array(
            'access_token' => 'PantaRei\\OAuth2\\Tests\\Model\\AccessToken',
            'authorize' => 'PantaRei\\OAuth2\\Tests\\Model\\Authorize',
            'client' => 'PantaRei\\OAuth2\\Tests\\Model\\Client',
            'code' => 'PantaRei\\OAuth2\\Tests\\Model\\Code',
            'refresh_token' => 'PantaRei\\OAuth2\\Tests\\Model\\RefreshToken',
            'scope' => 'PantaRei\\OAuth2\\Tests\\Model\\Scope',
            'user' => 'PantaRei\\OAuth2\\Tests\\Model\\User',
        );
        foreach ($models as $type => $model) {
            $modelManager = $app['oauth2.orm']->getRepository($model);
            $app['oauth2.model_manager.factory']->addModelManager($type, $modelManager);
        }

        // Add response type handler.
        foreach (array('code', 'token') as $type) {
            $app['oauth2.response_handler.factory']
                ->addResponseTypeHandler($type, $app['oauth2.response_handler.' . $type]);
        }

        // Add grant type handler.
        foreach (array('authorization_code', 'client_credentials', 'password', 'refresh_token') as $type) {
            $app['oauth2.grant_handler.factory']
                ->addGrantTypeHandler($type, $app['oauth2.grant_handler.' . $type]);
        }

        // Add token type handler.
        foreach (array('bearer', 'mac') as $type) {
            $app['oauth2.token_handler.factory']
                ->addTokenTypeHandler($type, $app['oauth2.token_handler.' . $type]);
        }

        $app['security.firewalls'] = array(
            'authorize' => array(
                'pattern' => '^/authorize',
                'http' => true,
                'users' => $app->share(function () use ($app) {
                    return $app['oauth2.model_manager.factory']->getModelManager('user');
                }),
            ),
            'token' => array(
                'pattern' => '^/token',
                'oauth2_token' => true,
            ),
            'resource' => array(
                'pattern' => '^/resource',
                'oauth2_resource' => true,
                'stateless' => true,
            ),
        );

        // Authorization endpoint.
        $app->get('/authorize', function (Request $request, Application $app) {
            return $app['oauth2.authorize_controller']->indexAction($request);
        });

        // Token endpoint.
        $app->post('/token', function (Request $request, Application $app) {
            return $app['oauth2.token_controller']->indexAction($request);
        });

        // Resource endpoint.
        $app->match('/resource/{echo}', function (Request $request, Application $app, $echo) {
            return new Response($echo);
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
        $em = $this->app['oauth2.orm'];
        $modelManagerFactory = $this->app['oauth2.model_manager.factory'];

        // Generate testing database schema.
        $classes = array(
            $em->getClassMetadata($modelManagerFactory->getModelManager('access_token')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('authorize')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('client')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('code')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('refresh_token')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('scope')->getClassName()),
            $em->getClassMetadata($modelManagerFactory->getModelManager('user')->getClassName()),
        );

        PersistentObject::setObjectManager($em);
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
    }

    private function addSampleData()
    {
        $modelManagerFactory = $this->app['oauth2.model_manager.factory'];

        // Add demo access token.
        $modelManager = $modelManagerFactory->getModelManager('access_token');
        $model = $modelManager->createAccessToken();
        $model->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setExpires(new \DateTime('+1 hours'))
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateAccessToken($model);

        // Add demo authorizes.
        $modelManager = $modelManagerFactory->getModelManager('authorize');
        $model = $modelManager->createAuthorize();
        $model->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateAuthorize($model);

        $model = $modelManager->createAuthorize();
        $model->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $modelManager->updateAuthorize($model);

        $model = $modelManager->createAuthorize();
        $model->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateAuthorize($model);

        $model = $modelManager->createAuthorize();
        $model->setClientId('http://democlient1.com/')
            ->setUsername('')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateAuthorize($model);

        // Add demo clients.
        $modelManager =  $modelManagerFactory->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient1.com/')
            ->setClientSecret('demosecret1')
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $modelManager->updateClient($model);

        $model = $modelManager->createClient();
        $model->setClientId('http://democlient2.com/')
            ->setClientSecret('demosecret2')
            ->setRedirectUri('http://democlient2.com/redirect_uri');
        $modelManager->updateClient($model);

        $model = $modelManager->createClient();
        $model->setClientId('http://democlient3.com/')
            ->setClientSecret('demosecret3')
            ->setRedirectUri('http://democlient3.com/redirect_uri');
        $modelManager->updateClient($model);

        // Add demo code.
        $modelManager = $modelManagerFactory->getModelManager('code');
        $model = $modelManager->createCode();
        $model->setCode('f0c68d250bcc729eb780a235371a9a55')
            ->setClientId('http://democlient2.com/')
            ->setRedirectUri('http://democlient2.com/redirect_uri')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $modelManager->updateCode($model);

        // Add demo refresh token.
        $modelManager = $modelManagerFactory->getModelManager('refresh_token');
        $model = $modelManager->createRefreshToken();
        $model->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setClientId('http://democlient3.com/')
            ->setExpires(new \DateTime('+1 days'))
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateRefreshToken($model);

        // Add demo scopes.
        $modelManager = $modelManagerFactory->getModelManager('scope');
        $model = $modelManager->createScope();
        $model->setScope('demoscope1');
        $modelManager->updateScope($model);

        $model = $modelManager->createScope();
        $model->setScope('demoscope2');
        $modelManager->updateScope($model);

        $model = $modelManager->createScope();
        $model->setScope('demoscope3');
        $modelManager->updateScope($model);

        // Add demo users.
        $modelManager = $modelManagerFactory->getModelManager('user');
        $model = $modelManager->createUser();
        $encoder = $this->app['security.encoder_factory']->getEncoder($model);
        $model->setUsername('demousername1')
            ->setPassword($encoder->encodePassword('demopassword1', $model->getSalt()));
        $modelManager->updateUser($model);

        $model = $modelManager->createUser();
        $encoder = $this->app['security.encoder_factory']->getEncoder($model);
        $model->setUsername('demousername2')
            ->setPassword($encoder->encodePassword('demopassword2', $model->getSalt()));
        $modelManager->updateUser($model);

        $model = $modelManager->createUser();
        $encoder = $this->app['security.encoder_factory']->getEncoder($model);
        $model->setUsername('demousername3')
            ->setPassword($encoder->encodePassword('demopassword3', $model->getSalt()));
        $modelManager->updateUser($model);
    }
}
