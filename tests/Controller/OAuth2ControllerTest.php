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
        $parameters = [
            'client_id' => '1234',
        ];
        $server = [
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/authorize', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testErrorBadResponseType()
    {
        $parameters = [
            'response_type' => 'foo',
            'client_id' => '1234',
            'redirect_uri' => 'http://example.com/redirect_uri',
        ];
        $server = [
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/authorize', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('unsupported_response_type', $tokenResponse['error']);
    }

    public function testExceptionNoGrantType()
    {
        $parameters = [
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionBadGrantType()
    {
        $parameters = [
            'grant_type' => 'foo',
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeNoClientId()
    {
        $parameters = [
            'grant_type' => 'authorization_code',
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBothClientId()
    {
        $parameters = [
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadBasicClientId()
    {
        $parameters = [
            'grant_type' => 'authorization_code',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://badclient1.com/',
            'PHP_AUTH_PW' => 'badsecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_client', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadPostClientId()
    {
        $parameters = [
            'grant_type' => 'authorization_code',
            'client_id' => 'http://badclient1.com/',
            'client_secret' => 'badsecret1',
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_client', $tokenResponse['error']);
    }

    public function testExceptionBadAccessToken()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', "aaa\x19bbb\x5Cccc\x7Fddd"]),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionNotExistsAccessToken()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'abcd']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionExpiredAccessToken()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'd2b58c4c6bc0cc9fefca2d558f1221a5']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $resourceResponse['error']);
    }

    public function testGoodAccessToken()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93']),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }
}
