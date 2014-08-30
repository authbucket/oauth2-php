<?php

/**
 * This file is part of the authbucket/oauth2-php package.
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
    public function testExceptionBadAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', "aaa\x19bbb\x5Cccc\x7Fddd")),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionNotExistsAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'abcd')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testExceptionExpiredAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'd2b58c4c6bc0cc9fefca2d558f1221a5')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $resourceResponse['error']);
    }

    public function testGoodAccessToken()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', 'eeb5aa92bbb4b56373b9e0d00bc02d93')),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
        $resourceResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $resourceResponse['username']);
    }
}
