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

use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Pantarei\OAuth2\Provider\OAuth2ServiceProvider;
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
class WebTestCase extends SilexWebTestCase
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
        $app['security.oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/Entity'), true);
            $event_manager = $app['dbs.event_manager']['default'];
            return EntityManager::create($conn, $config, $event_manager);
        });

        // Add model managers from ORM.
        $models = array(
            'access_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\AccessToken',
            'authorize' => 'Pantarei\\OAuth2\\Tests\\Entity\\Authorize',
            'client' => 'Pantarei\\OAuth2\\Tests\\Entity\\Client',
            'code' => 'Pantarei\\OAuth2\\Tests\\Entity\\Code',
            'refresh_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\RefreshToken',
            'scope' => 'Pantarei\\OAuth2\\Tests\\Entity\\Scope',
            'user' => 'Pantarei\\OAuth2\\Tests\\Entity\\User',
        );
        foreach ($models as $type => $model) {
            $modelManager = $app['security.oauth2.orm']->getRepository($model);
            $app['security.oauth2.model_manager.factory']->addModelManager($type, $modelManager);
        }

        // Add response type handler.
        foreach (array('code', 'token') as $type) {
            $app['security.oauth2.response_handler.factory']
                ->addResponseTypeHandler($type, $app['security.oauth2.response_handler.' . $type]);
        }

        // Add grant type handler.
        foreach (array('authorization_code', 'client_credentials', 'password', 'refresh_token') as $type) {
            $app['security.oauth2.grant_handler.factory']
                ->addGrantTypeHandler($type, $app['security.oauth2.grant_handler.' . $type]);
        }

        // Add token type handler.
        foreach (array('bearer', 'mac') as $type) {
            $app['security.oauth2.token_handler.factory']
                ->addTokenTypeHandler($type, $app['security.oauth2.token_handler.' . $type]);
        }

        $app['security.firewalls'] = array(
            'authorize' => array(
                'pattern' => '^/authorize',
                'http' => true,
                'users' => $app->share(function () use ($app) {
                    return $app['security.oauth2.model_manager.factory']->getModelManager('user');
                }),
            ),
            'token' => array(
                'pattern' => '^/token',
                'token' => true,
            ),
            'resource' => array(
                'pattern' => '^/resource',
                'resource' => true,
            ),
        );

        // Authorization endpoint.
        $app->get('/authorize', function (Request $request, Application $app) {
            return $app['security.oauth2.authorize_controller']($request, $app);
        });

        // Token endpoint.
        $app->post('/token', function (Request $request, Application $app) {
            return $app['security.oauth2.token_controller']($request, $app);
        });

        // Resource endpoint.
        $app->get('/resource/{echo}', function (Request $request, Application $app, $echo) {
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
        // Generate testing database schema.
        $classes = array(
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('access_token')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('authorize')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('client')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('code')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('refresh_token')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('scope')->getClassName()),
            $this->app['security.oauth2.orm']->getClassMetadata($this->app['security.oauth2.model_manager.factory']->getModelManager('user')->getClassName()),
        );

        PersistentObject::setObjectManager($this->app['security.oauth2.orm']);
        $tool = new SchemaTool($this->app['security.oauth2.orm']);
        $tool->createSchema($classes);
    }

    private function addSampleData()
    {
        // Add demo access token.
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('access_token');
        $model = $modelManager->createAccessToken();
        $model->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setExpires(time() + 28800)
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateAccessToken($model);

        // Add demo authorizes.
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('authorize');
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

        // Add demo clients.
        $modelManager =  $this->app['security.oauth2.model_manager.factory']->getModelManager('client');
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
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('code');
        $model = $modelManager->createCode();
        $model->setCode('f0c68d250bcc729eb780a235371a9a55')
            ->setClientId('http://democlient2.com/')
            ->setRedirectUri('http://democlient2.com/redirect_uri')
            ->setExpires(time() + 3600)
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $modelManager->updateCode($model);

        // Add demo refresh token.
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('refresh_token');
        $model = $modelManager->createRefreshToken();
        $model->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setClientId('http://democlient3.com/')
            ->setExpires(time() + 86400)
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $modelManager->updateRefreshToken($model);

        // Add demo scopes.
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('scope');
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
        $modelManager = $this->app['security.oauth2.model_manager.factory']->getModelManager('user');
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
