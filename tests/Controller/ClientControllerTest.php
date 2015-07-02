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

class ClientControllerTest extends WebTestCase
{
    public function testCreateActionJson()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($clientId, $response['clientId']);
    }

    public function testCreateActionXml()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($clientId, $response['clientId']);
    }

    public function testReadActionJson()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/client/1.json', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('51b2d34c3a661b5e111a694dfcb4b248', $response['clientId']);
    }

    public function testReadActionXml()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/client/1.xml', array(), array(), $server);
        $response = simplexml_load_string($client->getResponse()->getContent());
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('51b2d34c3a661b5e111a694dfcb4b248', $response['clientId']);
    }

    public function testUpdateActionJson()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($clientId, $response['clientId']);

        $id = $response['id'];
        $clientIdUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('clientId' => $clientIdUpdated), 'json');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/api/v1.0/client/${id}.json", array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($clientIdUpdated, $response['clientId']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/client/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($clientIdUpdated, $response['clientId']);
    }

    public function testUpdateActionXml()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($clientId, $response['clientId']);

        $id = $response['id'];
        $clientIdUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('clientId' => $clientIdUpdated), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/api/v1.0/client/${id}.xml", array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($clientIdUpdated, $response['clientId']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/client/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($clientIdUpdated, $response['clientId']);
    }

    public function testDeleteActionJson()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($clientId, $response['clientId']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/api/v1.0/client/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($clientId, $response['clientId']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/client/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response);
    }

    public function testDeleteActionXml()
    {
        $clientId = substr(md5(uniqid(null, true)), 0, 8);
        $clientSecret = substr(md5(uniqid(null, true)), 0, 8);
        $redirectUri = 'http://'.substr(md5(uniqid(null, true)), 0, 8).'.com';
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/client.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($clientId, $response['clientId']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/api/v1.0/client/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($clientId, $response['clientId']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/client/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response);
    }

    public function testListActionJson()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/client.json', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('51b2d34c3a661b5e111a694dfcb4b248', $response[0]['clientId']);
    }

    public function testListActionXml()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/client.xml', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('51b2d34c3a661b5e111a694dfcb4b248', $response[0]['clientId']);
    }
}
