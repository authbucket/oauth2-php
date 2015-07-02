<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\GrantType;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationCodeGrantTypeHandlerTest extends WebTestCase
{
    public function testExceptionAuthCodeNoSavedNoPassedRedirectUri()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => '08fb55e26c84f8cb060b7803bc177af8',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient4.com/',
            'PHP_AUTH_PW' => 'demosecret4',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadRedirectUri()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/wrong_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_grant', $tokenResponse['error']);
    }

    public function testExceptionAuthCodeBadRedirectUriFormat()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testErrorAuthCodeNoCode()
    {
        $request = new Request();
        $parameters = array(
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionWrongClientIdAuthCode()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_grant', $tokenResponse['error']);
    }

    public function testExceptionExpiredAuthCode()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => '1e5aa97ddaf4b0228dfb4223010d4417',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_grant', $tokenResponse['error']);
    }

    public function testGoodAuthCode()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
            'state' => 'f0c68d250bcc729eb780a235371a9a55',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
            'client_id' => 'http://democlient2.com/',
            'client_secret' => 'demosecret2',
            'state' => 'f0c68d250bcc729eb780a235371a9a55',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodAuthCodeNoPassedRedirectUri()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'client_id' => 'http://democlient2.com/',
            'client_secret' => 'demosecret2',
            'state' => 'f0c68d250bcc729eb780a235371a9a55',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodAuthCodeNoStoredRedirectUri()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => '08fb55e26c84f8cb060b7803bc177af8',
            'redirect_uri' => 'http://democlient4.com/redirect_uri',
            'client_id' => 'http://democlient4.com/',
            'client_secret' => 'demosecret4',
            'state' => '08fb55e26c84f8cb060b7803bc177af8',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
