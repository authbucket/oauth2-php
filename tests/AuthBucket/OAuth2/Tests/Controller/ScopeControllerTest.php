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

class ScopeControllerTest extends WebTestCase
{
    public function testCreateActionJson()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scope, $response['scope']);
    }

    public function testCreateActionXml()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scope, $response['scope']);
    }

    public function testReadActionJson()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/scope/1.json', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('demoscope1', $response['scope']);
    }

    public function testReadActionXml()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/scope/1.xml', array(), array(), $server);
        $response = simplexml_load_string($client->getResponse()->getContent());
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('demoscope1', $response['scope']);
    }

    public function testUpdateActionJson()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $scopeUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scopeUpdated), 'json');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/api/v1.0/scope/${id}.json", array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scopeUpdated, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/scope/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scopeUpdated, $response['scope']);
    }

    public function testUpdateActionXml()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $scopeUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scopeUpdated), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/api/v1.0/scope/${id}.xml", array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scopeUpdated, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/scope/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scopeUpdated, $response['scope']);
    }

    public function testDeleteActionJson()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.json', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/api/v1.0/scope/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($scope, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/scope/${id}.json", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response);
    }

    public function testDeleteActionXml()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $content = $this->app['serializer']->encode(array(
            'scope' => $scope,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/scope.xml', array(), array(), $server, $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/api/v1.0/scope/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($scope, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/api/v1.0/scope/${id}.xml", array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response);
    }

    public function testListActionJson()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/scope.json', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('demoscope1', $response[0]['scope']);
    }

    public function testListActionXml()
    {
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/scope.xml', array(), array(), $server);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('demoscope1', $response[0]['scope']);
    }
}
