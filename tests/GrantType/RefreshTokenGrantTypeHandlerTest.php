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

class RefreshTokenGrantTypeHandlerTest extends WebTestCase
{
    public function testErrorRefreshTokenNoToken()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'scope' => 'demoscope1 demoscope2 demoscope3',
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

    public function testErrorRefreshTokenBadScope()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => "badscope1",
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
        $this->assertEquals('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorRefreshTokenUnsupportedScope()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '302a7e7af27a25a6c052302d0dcac2c0',
            'scope' => 'unsupportedscope',
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
        $this->assertEquals('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorRefreshTokenUnauthorizedScope()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '302a7e7af27a25a6c052302d0dcac2c0',
            'scope' => 'demoscope4',
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
        $this->assertEquals('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorRefreshTokenBadScopeFormat()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
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
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionRefreshTokenBadClientId()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => 'demoscope1 demoscope2 demoscope3',
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

    public function testExceptionRefreshTokenExpired()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '5ff43cbc27b54202c6fd8bb9c2a308ce',
            'scope' => 'demoscope1',
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

    public function testGoodRefreshToken()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
