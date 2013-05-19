<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Provider;

use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Extension\ResponseType;
use Pantarei\OAuth2\OAuth2WebTestCase;
use Pantarei\OAuth2\Provider\AuthorizationControllerProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationControllerProviderTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->mount('/', new AuthorizationControllerProvider());

    return $app;
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionCodeNoClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionTokenNoClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function FAILED_testExceptionCodeBadClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://badclient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function FAILED_testExceptionTokenBadClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://badclient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionNoResponseType()
  {
    $request = new Request();
    $request->initialize(array(
      'client_id' => '1234',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionCodeNoSavedNoPassedRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionTokenNoSavedNoPassedRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionCodeBadRedirectUri()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/wrong_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionTokenBadRedirectUri()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/wrong_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedResponseTypeException
   */
  public function FAILED_testExceptionBadResponseType()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'foo',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionCodeBadScopeFormat()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function FAILED_testExceptionCodeBadScope()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "badscope1",
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionTokenBadScopeFormat()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function FAILED_testExceptionTokenBadScope()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "badscope1",
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function FAILED_testExceptionCodeBadStateFormat()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "demoscope1 demoscope2 demoscope3",
      'state' => "aaa\x19bbb\x7Fccc",
    ));
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.auth'] instanceof ResponseType);
  }

  public function testGoodCode()
  {
    $parameters = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testGoodCodeNoPassedRedirectUri() {
    // Insert client with redirect_uri, test empty pass in.
    $fixture = new Clients();
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
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testGoodCodeNoStoredRedirectUri() {
    // Insert client without redirect_uri, test valid pass in.
    $fixture = new Clients();
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
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testGoodToken()
  {
    $parameters = array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());

    $parameters = array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    );
    $client = $this->createClient();
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testGoodTokenNoPassedRedirectUri() {
    // Insert client with redirect_uri, test empty pass in.
    $fixture = new Clients();
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
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testGoodTokenNoStoredRedirectUri() {
    // Insert client without redirect_uri, test valid pass in.
    $fixture = new Clients();
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
    $crawler = $client->request('GET', '/', $parameters);
    $this->assertTrue($client->getResponse()->isOk());
  }
}
