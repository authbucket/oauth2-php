<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Controller;

use Pantarei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pantarei\OAuth2\Controller\AuthorizeController;

class AuthorizateControllerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionNoResponseType()
    {
        $parameters = array(
            'client_id' => '1234',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionCodeNoSavedNoPassedRedirectUri()
    {
        // Insert client without redirect_uri.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient4.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionTokenNoSavedNoPassedRedirectUri()
    {
        // Insert client without redirect_uri.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testErrorBadResponseType()
    {
        $parameters = array(
            'response_type' => 'foo',
            'client_id' => '1234',
            'redirect_uri' => 'http://example.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorCodeBadScopeFormat()
    {
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
     */
    public function testErrorCodeBadScope()
    {
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "badscope1",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorTokenBadScopeFormat()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
     */
    public function testErrorTokenBadScope()
    {
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => "badscope1",
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGoodCode()
    {
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeNoPassedRedirectUri()
    {
        // Insert client with redirect_uri, test empty pass in.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('http://democlient4.com/redirect_uri');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient4.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodCodeNoStoredRedirectUri()
    {
        // Insert client without redirect_uri, test valid pass in.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient5.com/')
            ->setClientSecret('demosecret5');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient5.com/',
            'redirect_uri' => 'http://democlient5.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodToken()
    {
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
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient3.com/',
            'redirect_uri' => 'http://democlient3.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername3',
            'PHP_AUTH_PW' => 'demopassword3',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenNoPassedRedirectUri()
    {
        // Insert client with redirect_uri, test empty pass in.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('http://democlient4.com/redirect_uri');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient4.com/',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testGoodTokenNoStoredRedirectUri()
    {
        // Insert client without redirect_uri, test valid pass in.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient5.com/')
            ->setClientSecret('demosecret5');
        $modelManager->updateClient($model);

        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient5.com/',
            'redirect_uri' => 'http://democlient5.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
