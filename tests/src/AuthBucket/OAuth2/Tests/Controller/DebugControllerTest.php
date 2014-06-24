<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\Controller;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DebugControllerTest extends WebTestCase
{
    public function testExceptionBadDebugToken()
    {
        $parameters = array(
            'debug' => "aaa\x19bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resource_response['error']);
    }

    public function testExceptionNotExistsDebugToken()
    {
        $parameters = array(
            'debug' => "abcd",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resource_response['error']);
    }

    public function testExceptionExpiredDebugToken()
    {
        $parameters = array(
            'debug' => "d2b58c4c6bc0cc9fefca2d558f1221a5",
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resource_response['error']);
    }

    public function testGoodEmptyDebugToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);
    }

    public function testGoodDebugToken()
    {
        $parameters = array(
            'debug' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resource_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resource_response['username']);
    }
}
