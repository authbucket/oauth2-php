<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\ResponseType;

use AuthBucket\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class TokenResponseTypeHandlerTest extends WebTestCase
{
    public function testExceptionTokenNoClientId()
    {
        $parameters = array(
            'response_type' => 'token',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionTokenBadClientId()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://badclient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('unauthorized_client', $tokenResponse['error']);
    }

    public function testExceptionTokenNoSavedNoPassedRedirectUri()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionTokenBadRedirectUri()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/wrong_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testExceptionTokenBadRedirectUriFormat()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testErrorTokenBadScopeFormat()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $tokenResponse = $authResponse->query->all();
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testErrorTokenUnsupportedScope()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "unsupportedscope",
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $tokenResponse = $authResponse->query->all();
        $this->assertEquals('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorTokenUnauthorizedScope()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "demoscope4",
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $tokenResponse = $authResponse->query->all();
        $this->assertEquals('invalid_scope', $tokenResponse['error']);
    }

    public function testErrorTokenBadStateFormat()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => "demoscope1 demoscope2 demoscope3",
            'state' => "aaa\x19bbb\x7Fccc",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $tokenResponse = $authResponse->query->all();
        $this->assertEquals('invalid_request', $tokenResponse['error']);
    }

    public function testGoodToken()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenNoPassedRedirectUri()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenNoStoredRedirectUri()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
            'redirect_uri' => 'http://democlient4.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenFormSubmit()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Must use single shared client for continue session.
        $client = $this->createClient();

        $crawler = $client->request('GET', '/oauth2/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'demousername3',
            '_password' => 'demopassword3',
        ));
        $client->submit($form);

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array();
        $crawler = $client->request('GET', '/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenFormSubmitRememberMe()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Save cookie REMEMBERME from first client.
        $client = $this->createClient();
        $crawler = $client->request('GET', '/oauth2/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'demousername3',
            '_password' => 'demopassword3',
            '_remember_me' => true,
        ));
        $client->submit($form);
        $rememberMe = $client->getCookieJar()->get('REMEMBERME');

        // Reuse cookie REMEMBERME for second client.
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array();
        $client = $this->createClient();
        $client->getCookieJar()->get($rememberMe);
        $crawler = $client->request('GET', '/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
