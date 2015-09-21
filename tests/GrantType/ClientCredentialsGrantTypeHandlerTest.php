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

class ClientCredentialsGrantTypeHandlerTest extends WebTestCase
{
    public function testErrorClientCredBadScope()
    {
        $parameters = [
            'grant_type' => 'client_credentials',
            'scope' => 'badscope1',
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

    public function testErrorClientCredBadScopeFormat()
    {
        $parameters = [
            'grant_type' => 'client_credentials',
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

    public function testGoodClientCred()
    {
        $parameters = [
            'grant_type' => 'client_credentials',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodClientCredNoScope()
    {
        $parameters = [
            'grant_type' => 'client_credentials',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
