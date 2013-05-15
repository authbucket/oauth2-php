<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Request;

use Pantarei\OAuth2\Request\AuthorizationRequest;
use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequestTest extends OAuth2WebTestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoClientId()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testValidateRequestBadClientId()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://badclient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoRedirectUri()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'client_id' => '1234',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoSavedNoPassedRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['orm']->persist($client);
    $this->app['orm']->flush();

    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestWongSavedRedirectUri()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/wrong_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoResponseType()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedResponseTypeException
   */
  public function testValidateRequestBadResponseType()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'foo',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadScope()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestNotExistsScope()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "badscope1",
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadState()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "demoscope1 demoscope2 demoscope3",
      'state' => "aaa\x19bbb\x7Fccc",
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('http://democlient4.com/redirect_uri');
    $this->app['orm']->persist($client);
    $this->app['orm']->flush();

    $controller = new AuthorizationRequest($this->app);
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
      'redirect_uri' => 'http://democlient4.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodResponseType()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));

    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodScope()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodState()
  {
    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest($this->app);
    $this->assertTrue(is_object($filtered_query));
  }
}
