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

use AuthBucket\OAuth2\Controller\TokenController;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Provider\AuthBucketOAuth2ServiceProvider;
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
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

/**
 * Test Silex service provider without model manager initialization.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class WebTestCaseNullModelManagerTest extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app->register(new AuthBucketOAuth2ServiceProvider());
        $app->register(new DoctrineServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new TwigServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());

        // Return an instance of Doctrine ORM entity manager.
        $app['authbucket_oauth2.orm'] = $app->share(function ($app) {
            $conn = $app['dbs']['default'];
            $eventManager = $app['dbs.event_manager']['default'];

            $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__ . '/../tests/src/AuthBucket/OAuth2/Tests/TestBundle/Entity'));

            $config = Setup::createConfiguration(false);
            $config->setMetadataDriverImpl($driver);
            $config->setMetadataCacheImpl(new ArrayCache());
            $config->setQueryCacheImpl(new ArrayCache());

            return EntityManager::create($conn, $config, $eventManager);
        });

        // Fake lib dev, simply use plain text encoder.
        $app['security.encoder.digest'] = $app->share(function ($app) {
            return new PlaintextPasswordEncoder();
        });

        // We simply reuse the user provider that already created for
        // authorize firewall here.
        $app['authbucket_oauth2.token_controller'] = $app->share(function () use ($app) {
            return new TokenController(
                $app['security'],
                $app['security.user_checker'],
                $app['security.encoder_factory'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.grant_handler.factory'],
                $app['authbucket_oauth2.token_handler.factory'],
                $app['security.user_provider.auth_default']
            );
        });

        require __DIR__ . '/../../../../../app/config/config_test.php';
        require __DIR__ . '/../../../../../app/config/routing_test.php';

        return $app;
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testNullModelManager()
    {
        // Query the token endpoint with grant_type = client_credentials.
        $parameters = array(
            'grant_type' => 'client_credentials',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $tokenResponse['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = array(
            'debug_token' => $tokenResponse['access_token'],
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('', $resourceResponse['username']);
    }
}
