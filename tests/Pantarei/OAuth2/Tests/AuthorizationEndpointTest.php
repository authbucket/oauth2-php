<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests;

use Pantarei\OAuth2\Extension\ResponseType;
use Pantarei\OAuth2\WebTestCase;
use Pantarei\OAuth2\Provider\OAuth2ControllerProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization endpoint functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationEndpointTest extends WebTestCase
{
    public function testExceptionCodeNoClientId()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionTokenNoClientId()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionCodeBadClientId()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://badclient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionTokenBadClientId()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://badclient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionNoResponseType()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'client_id' => '1234',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionCodeNoSavedNoPassedRedirectUri()
    {
        // Insert client without redirect_uri.
        $client = new $this->app['oauth2.entity']['Clients']();
        $client->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('');
        $this->app['oauth2.orm']->persist($client);
        $this->app['oauth2.orm']->flush();

        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient4.com/',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionTokenNoSavedNoPassedRedirectUri()
    {
        // Insert client without redirect_uri.
        $client = new $this->app['oauth2.entity']['Clients']();
        $client->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('');
        $this->app['oauth2.orm']->persist($client);
        $this->app['oauth2.orm']->flush();

        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionCodeBadRedirectUri()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/wrong_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testExceptionTokenBadRedirectUri()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/wrong_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorBadResponseType()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'foo',
            'client_id' => '1234',
            'redirect_uri' => 'http://example.com/redirect_uri',
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorCodeBadScopeFormat()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorCodeBadScope()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "badscope1",
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorTokenBadScopeFormat()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorTokenBadScope()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "badscope1",
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testErrorCodeBadStateFormat()
    {
        $app = new Application;
        $app['debug'] = true;
        $app->mount('/', new OAuth2ControllerProvider());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "demoscope1 demoscope2 demoscope3",
            'state' => "aaa\x19bbb\x7Fccc",
        );
        $request = Request::create('/authorize', 'GET', $parameters);
        $response = $app->handle($request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testGoodCode()
    {
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeNoPassedRedirectUri() 
    {
        // Insert client with redirect_uri, test empty pass in.
        $fixture = new $this->app['oauth2.entity']['Clients']();
        $fixture->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('http://democlient4.com/redirect_uri');
        $this->app['oauth2.orm']->persist($fixture);
        $this->app['oauth2.orm']->flush();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient4.com/',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeNoStoredRedirectUri() 
    {
        // Insert client without redirect_uri, test valid pass in.
        $fixture = new $this->app['oauth2.entity']['Clients']();
        $fixture->setClientId('http://democlient5.com/')
            ->setClientSecret('demosecret5')
            ->setRedirectUri('');
        $this->app['oauth2.orm']->persist($fixture);
        $this->app['oauth2.orm']->flush();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient5.com/',
            'redirect_uri' => 'http://democlient5.com/redirect_uri',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodToken()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodTokenNoPassedRedirectUri() 
    {
        // Insert client with redirect_uri, test empty pass in.
        $fixture = new $this->app['oauth2.entity']['Clients']();
        $fixture->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('http://democlient4.com/redirect_uri');
        $this->app['oauth2.orm']->persist($fixture);
        $this->app['oauth2.orm']->flush();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    public function testGoodTokenNoStoredRedirectUri() 
    {
        // Insert client without redirect_uri, test valid pass in.
        $fixture = new $this->app['oauth2.entity']['Clients']();
        $fixture->setClientId('http://democlient5.com/')
            ->setClientSecret('demosecret5')
            ->setRedirectUri('');
        $this->app['oauth2.orm']->persist($fixture);
        $this->app['oauth2.orm']->flush();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient5.com/',
            'redirect_uri' => 'http://democlient5.com/redirect_uri',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
