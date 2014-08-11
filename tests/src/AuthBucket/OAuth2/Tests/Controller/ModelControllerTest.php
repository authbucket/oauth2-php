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

    public function testUpdateModelJson()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scope), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $scopeUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scopeUpdated), 'json');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/oauth2/model/scope/${id}.json", array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scopeUpdated, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/scope/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scopeUpdated, $response['scope']);
    }

    public function testUpdateModelXml()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scope), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $scopeUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scopeUpdated), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/oauth2/model/scope/${id}.xml", array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scopeUpdated, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/scope/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scopeUpdated, $response['scope']);
    }

    public function testDeleteModelJson()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scope), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/oauth2/model/scope/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($scope, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/scope/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response);
    }

    public function testDeleteModelXml()
    {
        $scope = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('scope' => $scope), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/scope.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($scope, $response['scope']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/oauth2/model/scope/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($scope, $response['scope']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/scope/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response);
    }
}
