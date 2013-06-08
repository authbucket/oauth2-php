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
use Pantarei\OAuth2\Security\Authentication\Provider\BearerTokenProvider;
use Pantarei\OAuth2\Security\Authentication\Provider\TokenProvider;
use Pantarei\OAuth2\Security\Firewall\BearerTokenListener;
use Pantarei\OAuth2\Security\Firewall\TokenListener;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
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
        $app->register(new OAuth2ServiceProvider());

        $app['db.options'] = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        // Return an instance of Doctrine ORM entity manager.
        $app['oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/Entity'), true);
            $event_manager = $app['dbs.event_manager']['default'];
            return EntityManager::create($conn, $config, $event_manager);
        });

        // Shortcut for entity.
        $entity = array(
            'access_token' => 'Pantarei\OAuth2\Tests\Entity\AccessToken',
            'authorize' => 'Pantarei\OAuth2\Tests\Entity\Authorize',
            'client' => 'Pantarei\OAuth2\Tests\Entity\Client',
            'code' => 'Pantarei\OAuth2\Tests\Entity\Code',
            'refresh_token' => 'Pantarei\OAuth2\Tests\Entity\RefreshToken',
            'scope' => 'Pantarei\OAuth2\Tests\Entity\Scope',
            'user' => 'Pantarei\OAuth2\Tests\Entity\User',
        );
        foreach ($entity as $name => $class) {
            $app['oauth2.model.' . $name] = $class;
            $app['oauth2.model_manager.' . $name] = $app['oauth2.orm']->getRepository($class);
        }

        $app['security.authentication_listener.factory.token'] = $app->protect(function ($name, $options) use ($app) {
            $app['security.authentication_provider.' . $name . '.token'] = $app->share(function () use ($app, $name) {
                return new TokenProvider(
                    $app['oauth2.model_manager.client']
                );
            });
            $app['security.authentication_listener.' . $name . '.token'] = $app->share(function () use ($app, $name) {
                return new TokenListener(
                    $app['security'],
                    $app['security.authentication_manager']
                );
            });

            return array(
                'security.authentication_provider.' . $name . '.token',
                'security.authentication_listener.' . $name . '.token',
                null,
                'pre_auth',
            );
        });
        $app['security.authentication_listener.factory.resource'] = $app->protect(function ($name, $options) use ($app) {
            $app['security.authentication_provider.' . $name . '.resource'] = $app->share(function () use ($app, $name) {
                return new BearerTokenProvider(
                    $app['oauth2.model_manager.access_token']
                );
            });
            $app['security.authentication_listener.' . $name . '.resource'] = $app->share(function () use ($app, $name) {
                return new BearerTokenListener(
                    $app['security'],
                    $app['security.authentication_manager']
                );
            });

            return array(
                'security.authentication_provider.' . $name . '.resource',
                'security.authentication_listener.' . $name . '.resource',
                null,
                'pre_auth',
            );
        });

        $app['security.firewalls'] = array(
            'authorize' => array(
                'pattern' => '^/authorize',
                'http' => true,
                'users' => $app->share(function () use ($app) {
                    return $app['oauth2.model_manager.user'];
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
            $response_type = ParameterUtils::checkResponseType($request, $app);
            $controller = $app['oauth2.response_type.' . $response_type]::create($request, $app);
            return $controller->getResponse($request, $app);
        });

        // Token endpoint.
        $app->post('/token', function (Request $request, Application $app) {
            $grant_type = ParameterUtils::checkGrantType($request, $app);
            $controller = $app['oauth2.grant_type.' . $grant_type]::create($request, $app);
            return $controller->getResponse($request, $app);
        });

        // Resource endpoint.
        $app->get('/resource/{username}', function (Request $request, Application $app, $username) {
            return new Response($username);
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
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.access_token']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.authorize']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.client']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.code']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.refresh_token']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.scope']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.model.user']),
        );

        PersistentObject::setObjectManager($this->app['oauth2.orm']);
        $tool = new SchemaTool($this->app['oauth2.orm']);
        $tool->createSchema($classes);
    }

    private function addSampleData()
    {
        // Add demo access token.
        $entity = new $this->app['oauth2.model.access_token']();
        $entity->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setExpires(time() + 28800)
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->app['oauth2.orm']->persist($entity);

        // Add demo authorizes.
        $entity = new $this->app['oauth2.model.authorize']();
        $entity->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.authorize']();
        $entity->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.authorize']();
        $entity->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $this->app['oauth2.orm']->persist($entity);

        // Add demo clients.
        $entity = new $this->app['oauth2.model.client']();
        $entity->setClientId('http://democlient1.com/')
            ->setClientSecret('demosecret1')
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.client']();
        $entity->setClientId('http://democlient2.com/')
            ->setClientSecret('demosecret2')
            ->setRedirectUri('http://democlient2.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.client']();
        $entity->setClientId('http://democlient3.com/')
            ->setClientSecret('demosecret3')
            ->setRedirectUri('http://democlient3.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        // Add demo code.
        $entity = new $this->app['oauth2.model.code']();
        $entity->setCode('f0c68d250bcc729eb780a235371a9a55')
            ->setClientId('http://democlient2.com/')
            ->setRedirectUri('http://democlient2.com/redirect_uri')
            ->setExpires(time() + 3600)
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $this->app['oauth2.orm']->persist($entity);

        // Add demo refresh token.
        $entity = new $this->app['oauth2.model.refresh_token']();
        $entity->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setClientId('http://democlient3.com/')
            ->setExpires(time() + 86400)
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $this->app['oauth2.orm']->persist($entity);

        // Add demo scopes.
        $entity = new $this->app['oauth2.model.scope']();
        $entity->setScope('demoscope1');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.scope']();
        $entity->setScope('demoscope2');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.scope']();
        $entity->setScope('demoscope3');
        $this->app['oauth2.orm']->persist($entity);

        // Add demo users.
        $entity = new $this->app['oauth2.model.user']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername1')
            ->setPassword($encoder->encodePassword('demopassword1', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.user']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername2')
            ->setPassword($encoder->encodePassword('demopassword2', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.model.user']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername3')
            ->setPassword($encoder->encodePassword('demopassword3', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        // Flush all records to database
        $this->app['oauth2.orm']->flush();
    }
}
