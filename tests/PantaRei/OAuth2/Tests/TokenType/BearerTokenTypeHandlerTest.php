<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests\TokenType;

use PantaRei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionNoToken()
    {
        $parameters = array();
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionDuplicateToken()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testAuthorizationHeader()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());

        $parameters = array();
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testGet()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testPost()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }
}
