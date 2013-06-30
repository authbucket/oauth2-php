<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Controller;

use Pantarei\Oauth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenControllerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
     */
    public function testExceptionNoGrantType()
    {
        $parameters = array(
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
     */
    public function testExceptionBadGrantType()
    {
        $parameters = array(
            'grant_type' => 'foo',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeNoClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeBothClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
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
     * @expectedException \Pantarei\Oauth2\Exception\InvalidClientException
     */
    public function testExceptionAuthCodeBadBasicClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://badclient1.com/',
            'PHP_AUTH_PW' => 'badsecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\InvalidClientException
     */
    public function testExceptionAuthCodeBadPostClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://badclient1.com/',
            'client_secret' => 'badsecret1',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
