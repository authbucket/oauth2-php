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
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($username, $response['username']);
    }

    public function testCreateModelXml()
    {
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($username, $response['username']);
    }

    public function testReadModelJson()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/user/1.json');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('demousername1', $response['username']);
    }

    public function testReadModelXml()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/user/1.xml');
        $response = simplexml_load_string($client->getResponse()->getContent());
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('demousername1', $response['username']);
    }

    public function testUpdateModelJson()
    {
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($username, $response['username']);

        $id = $response['id'];
        $usernameUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('username' => $usernameUpdated), 'json');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/oauth2/model/user/${id}.json", array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($usernameUpdated, $response['username']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/user/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($usernameUpdated, $response['username']);
    }

    public function testUpdateModelXml()
    {
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($username, $response['username']);

        $id = $response['id'];
        $usernameUpdated = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array('username' => $usernameUpdated), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('PUT', "/oauth2/model/user/${id}.xml", array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($usernameUpdated, $response['username']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/user/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($usernameUpdated, $response['username']);
    }

    public function testDeleteModelJson()
    {
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'json');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.json', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals($username, $response['username']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/oauth2/model/user/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($username, $response['username']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/user/${id}.json");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals(null, $response);
    }

    public function testDeleteModelXml()
    {
        $username = substr(md5(uniqid(null, true)), 0, 8);
        $password = substr(md5(uniqid(null, true)), 0, 8);
        $content = $this->app['serializer']->encode(array(
            'username' => $username,
            'password' => $password,
        ), 'xml');
        $client = $this->createClient();
        $crawler = $client->request('POST', '/oauth2/model/user.xml', array(), array(), array(), $content);
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals($username, $response['username']);

        $id = $response['id'];
        $client = $this->createClient();
        $crawler = $client->request('DELETE', "/oauth2/model/user/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response['id']);
        $this->assertEquals($username, $response['username']);

        $client = $this->createClient();
        $crawler = $client->request('GET', "/oauth2/model/user/${id}.xml");
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals(null, $response);
    }

    public function testListModelJson()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/user.json');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'json');
        $this->assertEquals('demousername1', $response[0]['username']);
    }

    public function testListModelXml()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/model/user.xml');
        $response = $this->app['serializer']->decode($client->getResponse()->getContent(), 'xml');
        $this->assertEquals('demousername1', $response[0]['username']);
    }
}
