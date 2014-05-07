<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests;

use AuthBucket\OAuth2\Provider\OAuth2ServiceProvider;
use AuthBucket\OAuth2\Tests\Entity\ModelManagerFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class NullUserProviderTest extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app->register(new DoctrineServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new OAuth2ServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new TwigServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());

        // Return an instance of Doctrine ORM entity manager.
        $app['authbucket_oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $event_manager = $app['dbs.event_manager']['default'];

            $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__ . '/../src/AuthBucket/OAuth2/Tests/Entity'));

            $config = Setup::createConfiguration(false);
            $config->setMetadataDriverImpl($driver);
            $config->setMetadataCacheImpl(new ArrayCache());
            $config->setQueryCacheImpl(new ArrayCache());

            return EntityManager::create($conn, $config, $event_manager);
        });

        // Fake lib dev, simply use plain text encoder.
        $app['security.encoder.digest'] = $app->share(function ($app) {
            return new PlaintextPasswordEncoder();
        });

        // Add model managers from ORM.
        $app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory($app['authbucket_oauth2.orm'], $app['authbucket_oauth2.model']);
        });

        require __DIR__ . '/../../../../app/config/config_test.php';
        require __DIR__ . '/../../../../app/config/routing.php';

        return $app;
    }

    public function testNullUserProvider()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername3',
            'password' => 'demopassword3',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'demostate1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('server_error', $token_response['error']);
    }
}
