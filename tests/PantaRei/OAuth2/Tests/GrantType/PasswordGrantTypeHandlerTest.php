<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests\GrantType;

use PantaRei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordGrantTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorPasswordNoUsername()
    {
        $parameters = array(
            'grant_type' => 'password',
            'password' => 'demopassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorPasswordNoPassword()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionPasswordBadPassword()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'badpassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'demostate1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionPasswordBadUsername()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'badusername1',
            'password' => 'badpassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'demostate1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidScopeException
     */
    public function testErrorPasswordBadScope()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => "badscope1",
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorPasswordBadScopeFormat()
    {
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
