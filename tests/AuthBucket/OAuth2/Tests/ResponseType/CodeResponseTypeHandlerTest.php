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

class CodeResponseTypeHandlerTest extends WebTestCase
{


    public function testExceptionCodeNoClientId()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testExceptionCodeBadClientId()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testExceptionCodeNoSavedNoPassedRedirectUri()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testExceptionCodeBadRedirectUri()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testExceptionCodeBadRedirectUriFormat()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testErrorCodeBadScopeFormat()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
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

    public function testErrorCodeUnsupportedScope()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
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

    public function testErrorCodeUnauthorizedScope()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
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

    public function testErrorCodeBadStateFormat()
    {
        $parameters = array(
            'response_type' => 'code',
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

    public function testGoodCode()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
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
            'response_type' => 'code',
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
            'response_type' => 'code',
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
            'response_type' => 'code',
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

    public function testGoodCodeNoPassedRedirectUri()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeNoStoredRedirectUri()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient4.com/',
            'redirect_uri' => 'http://democlient4.com/redirect_uri',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeFormSubmit()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Must use two clients for different users
        $client = $this->createClient();

        $crawler = $client->request('GET', '/oauth2/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'demousername3',
            '_password' => 'demopassword3',
            "_remember_me" => true,
        ));
        $client->submit($form);

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array();
        $crawler = $client->request('GET', '/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->getTargetUrl() != "http://localhost/oauth2/login");
    }

    public function testRememberMeToken()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Must use two clients for different users
        $client = $this->createClient();

        $crawler = $client->request('GET', '/oauth2/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'demousername3',
            '_password' => 'demopassword3',
            "_remember_me" => true,
        ));
        $client->submit($form);

        $remembermeCookie = $client->getCookieJar()->get('REMEMBERME');
        $client->restart();
        $client->getCookieJar()->set($remembermeCookie);

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array();
        $crawler = $client->request('GET', '/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $this->assertTrue($client->getResponse()->getTargetUrl() != "http://localhost/oauth2/login");
    }


    public function testNoRememberMeToken()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // If we use a two clients and no remember_me, it should not work
        $client = $this->createClient();

        $crawler = $client->request('GET', '/oauth2/login');
        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            '_username' => 'demousername3',
            '_password' => 'demopassword3',
            '_remember_me' => false,
        ));
        $client->submit($form);



        $client->restart();

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => $session->getId(),
        );
        $server = array();

        $crawler = $client->request('GET', '/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->getTargetUrl() == "http://localhost/oauth2/login");
    }
}
