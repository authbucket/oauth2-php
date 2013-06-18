<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\GrantType;

use Pantarei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientCredentialsGrantTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
     */
    public function testErrorClientCredBadScope()
    {
        $parameters = array(
            'grant_type' => 'client_credentials',
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
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorClientCredBadScopeFormat()
    {
        $parameters = array(
            'grant_type' => 'client_credentials',
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
