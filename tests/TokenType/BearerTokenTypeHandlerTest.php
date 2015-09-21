<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TokenType;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class BearerTokenTypeHandlerTest extends WebTestCase
{
    public function testExceptionNoToken()
    {
        $parameters = [];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionDuplicateToken()
    {
        $parameters = [
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $server = [
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_request', $tokenResponse['error']);
    }

    public function testAuthorizationHeader()
    {
        $parameters = [];
        $server = [
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);

        $parameters = [];
        $server = [
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }

    public function testGet()
    {
        $parameters = [
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }

    public function testPost()
    {
        $parameters = [
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/debug', $parameters, [], $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $resourceResponse['username']);
    }
}
