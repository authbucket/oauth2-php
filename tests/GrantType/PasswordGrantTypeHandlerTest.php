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

class PasswordGrantTypeHandlerTest extends WebTestCase
{
    public function testErrorPasswordNoUsername()
    {
        $parameters = [
            'grant_type' => 'password',
            'password' => 'demopassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testErrorPasswordNoPassword()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionPasswordBadPassword()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'badpassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_grant', $tokenResponse['error']);
    }

    public function testExceptionPasswordBadUsername()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'badusername1',
            'password' => 'badpassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_grant', $tokenResponse['error']);
    }

    public function testErrorPasswordUnsupportedScope()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'unsupportedscope',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorPasswordUnauthorizedScope()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope4',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorPasswordBadScopeFormat()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testGoodPassword()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername3',
            'password' => 'demopassword3',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodPasswordNoScope()
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername3',
            'password' => 'demopassword3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
