<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\Controller;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class OAuth2ControllerTest extends WebTestCase
{
    public function testExceptionNoResponseType()
    {
        $parameters = array(
            'client_id' => '1234',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testErrorBadResponseType()
    {
        $parameters = array(
            'response_type' => 'foo',
            'client_id' => '1234',
            'redirect_uri' => 'http://example.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('unsupported_response_type', $tokenResponse['error']);
    }

    public function testExceptionNoGrantType()
    {
        $parameters = array(
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionBadGrantType()
    {
        $parameters = array(
            'grant_type' => 'foo',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeNoClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBothClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadBasicClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://badclient1.com/',
            'PHP_AUTH_PW' => 'badsecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_client', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadPostClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://badclient1.com/',
            'client_secret' => 'badsecret1',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_client', $tokenResponse['error']);
    }

    public function testExceptionBadAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', "aaa\x19bbb\x5Cccc\x7Fddd")),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionNotExistsAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'abcd')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionExpiredAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'd2b58c4c6bc0cc9fefca2d558f1221a5')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testGoodAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resourceResponse['username']);
    }

    public function testCron()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/cron', $parameters, array(), $server);

        $modelManagerFactory = $this->app['authbucket_oauth2.model_manager.factory'];
        $this->assertEmpty($modelManagerFactory->getModelManager('access_token')
            ->readModelBy(array(
                'accessToken' => 'd2b58c4c6bc0cc9fefca2d558f1221a5',
            )));
        $this->assertEmpty($modelManagerFactory->getModelManager('code')
            ->readModelBy(array(
                'code' => '1e5aa97ddaf4b0228dfb4223010d4417',
            )));
        $this->assertEmpty($modelManagerFactory->getModelManager('refresh_token')
            ->readModelBy(array(
                'refreshToken' => '5ff43cbc27b54202c6fd8bb9c2a308ce',
            )));
    }
}
