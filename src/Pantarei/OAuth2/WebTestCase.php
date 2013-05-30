<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2;

use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\Tools\SchemaTool;
use Pantarei\OAuth2\Provider\OAuth2ControllerProvider;
use Pantarei\OAuth2\Provider\OAuth2ServiceProvider;
use Pantarei\OAuth2\Provider\UserProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\WebTestCase as SilexWebTestCase;

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

        $app->mount('/', new OAuth2ControllerProvider());

        $app['db.options'] = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );
        $app['security.firewalls'] = array(
            'authorize' => array(
                'pattern' => '^/authorize',
                'http' => true,
                'users' => $app->share(function () use ($app) {
                    return new UserProvider($app);
                }),
            ),
        );

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
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.access_tokens']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.authorizes']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.clients']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.codes']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.refresh_tokens']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.scopes']),
            $this->app['oauth2.orm']->getClassMetadata($this->app['oauth2.entity.users']),
        );

        PersistentObject::setObjectManager($this->app['oauth2.orm']);
        $tool = new SchemaTool($this->app['oauth2.orm']);
        $tool->createSchema($classes);
    }

    private function addSampleData()
    {
        // Add demo access token.
        $entity = new $this->app['oauth2.entity.access_tokens']();
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
        $entity = new $this->app['oauth2.entity.authorizes']();
        $entity->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.authorizes']();
        $entity->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.authorizes']();
        $entity->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $this->app['oauth2.orm']->persist($entity);

        // Add demo clients.
        $entity = new $this->app['oauth2.entity.clients']();
        $entity->setClientId('http://democlient1.com/')
            ->setClientSecret('demosecret1')
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.clients']();
        $entity->setClientId('http://democlient2.com/')
            ->setClientSecret('demosecret2')
            ->setRedirectUri('http://democlient2.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.clients']();
        $entity->setClientId('http://democlient3.com/')
            ->setClientSecret('demosecret3')
            ->setRedirectUri('http://democlient3.com/redirect_uri');
        $this->app['oauth2.orm']->persist($entity);

        // Add demo code.
        $entity = new $this->app['oauth2.entity.codes']();
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
        $entity = new $this->app['oauth2.entity.refresh_tokens']();
        $entity->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setTokenType('bearer')
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
        $entity = new $this->app['oauth2.entity.scopes']();
        $entity->setScope('demoscope1');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.scopes']();
        $entity->setScope('demoscope2');
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.scopes']();
        $entity->setScope('demoscope3');
        $this->app['oauth2.orm']->persist($entity);

        // Add demo users.
        $entity = new $this->app['oauth2.entity.users']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername1')
            ->setPassword($encoder->encodePassword('demopassword1', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.users']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername2')
            ->setPassword($encoder->encodePassword('demopassword2', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        $entity = new $this->app['oauth2.entity.users']();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername3')
            ->setPassword($encoder->encodePassword('demopassword3', $entity->getSalt()));
        $this->app['oauth2.orm']->persist($entity);

        // Flush all records to database
        $this->app['oauth2.orm']->flush();
    }
}
