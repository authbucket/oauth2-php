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

class ModelControllerTest extends WebTestCase
{
    public function testReadModelJSON()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope/1.json');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('debug', $response['scope']);
    }

    public function testReadModelXML()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope/1.xml');
        $response = simplexml_load_string($client->getResponse()->getContent());
        $this->assertEquals('debug', $response->scope);
    }

    public function testReadModelAllJSON()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope.json');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(5, count($response));
        $this->assertEquals('debug', $response[0]['scope']);
    }

    public function testReadModelAllXML()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope.xml');
        $response = simplexml_load_string($client->getResponse()->getContent());
        $this->assertEquals(5, count($response));
        $this->assertEquals('debug', $response->item[0]->scope);
    }
}
