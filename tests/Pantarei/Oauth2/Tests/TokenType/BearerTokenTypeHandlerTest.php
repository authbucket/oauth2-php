<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\TokenType;

use Pantarei\Oauth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenTypeHandlerTest extends WebTestCase
{
    public function testExceptionNoToken()
    {
        $parameters = array();
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/username', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $token_response['error']);
    }

    public function testExceptionDuplicateToken()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/username', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $token_response['error']);
    }

    public function testAuthorizationHeader()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/username', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);

        $parameters = array();
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/resource/username', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);
    }

    public function testGet()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/username', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);
    }

    public function testPost()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/resource/username', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);
    }
}
