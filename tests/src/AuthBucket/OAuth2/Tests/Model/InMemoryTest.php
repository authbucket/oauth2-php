<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\Model;

use AuthBucket\OAuth2\Model\InMemory\ModelManagerFactory;
use AuthBucket\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class InMemoryTest extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = new Application(array('env' => 'test'));

        require __DIR__ . '/../../../../../../app/AppKernel.php';

        $app['authbucket_oauth2.model'] = array(
            'access_token' => 'AuthBucket\\OAuth2\\Model\\InMemory\\AccessToken',
        );

        $app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory($app['authbucket_oauth2.model']);
        });

        $accessTokenManager = $app['authbucket_oauth2.model_manager.factory']->getModelManager('access_token');
        $className = $accessTokenManager->getClassName();

        $model = new $className();
        $model->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setExpires(new \DateTime('+1 hours'))
            ->setScope(array(
                'debug',
                'demoscope1',
            ));
        $accessTokenManager->createModel($model);

        $model = new $className();
        $model->setAccessToken('d2b58c4c6bc0cc9fefca2d558f1221a5')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setExpires(new \DateTime('-1 hours'))
            ->setScope(array(
                'demoscope1',
            ));
        $accessTokenManager->createModel($model);

        $app->boot();

        return $app;
    }

    public function testExceptionNotExistsAccessToken()
    {
        $parameters = array(
            'debug_token' => "eeb5aa92bbb4b56373b9e0d00bc02d93",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'abcd')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionExpiredAccessToken()
    {
        $parameters = array(
            'debug_token' => "eeb5aa92bbb4b56373b9e0d00bc02d93",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'd2b58c4c6bc0cc9fefca2d558f1221a5')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionBadDebugToken()
    {
        $parameters = array(
            'debug_token' => "aaa\x19bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionNotExistsDebugToken()
    {
        $parameters = array(
            'debug_token' => "abcd",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionExpiredDebugToken()
    {
        $parameters = array(
            'debug_token' => "d2b58c4c6bc0cc9fefca2d558f1221a5",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionInvalidParameter()
    {
        $parameters = array(
            'debug_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint/invalid_options', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('server_error', $resourceResponse['error']);
    }

    public function testGoodEmptyDebugToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resourceResponse['username']);
    }

    public function testGoodDebugToken()
    {
        $parameters = array(
            'debug_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resourceResponse['username']);
    }

    public function testGoodCacheDebugToken()
    {
        $parameters = array(
            'debug_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/resource_type/debug_endpoint/cache', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resourceResponse['username']);
    }
}
