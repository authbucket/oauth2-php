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
    public function testCreateModelJson()
    {
        $content = $this->app['serializer']->encode(array('scope' => 'demoscopeJson'), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('demoscopeJson', $response['scope']);
    }

    public function testCreateModelXml()
    {
        $content = $this->app['serializer']->encode(array('scope' => 'demoscopeXml'), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('demoscopeXml', $response['scope']);
    }

    public function testReadModelJson()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope/1.json');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('debug', $response['scope']);
    }

    public function testReadModelXml()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope/1.xml');
        $response = simplexml_load_string($client->getResponse()->getContent());
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('debug', $response['scope']);
    }

    public function testReadModelAllJson()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope.json');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('debug', $response[0]['scope']);
    }

    public function testReadModelAllXml()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/scope.xml');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('debug', $response[0]['scope']);
    }
}
