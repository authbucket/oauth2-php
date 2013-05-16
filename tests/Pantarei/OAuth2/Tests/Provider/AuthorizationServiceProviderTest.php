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
use Pantarei\OAuth2\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationServiceProviderTest extends OAuth2WebTestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $this->app['oauth2.auth.options.initializer']();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testValidateRequestBadClientId()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://badclient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoRedirectUri()
  {
    $request = new Request();
    $request->initialize(array(
      'client_id' => '1234',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
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
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestWongSavedRedirectUri()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/wrong_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoResponseType()
  {
    $request = new Request();
    $request->initialize(array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedResponseTypeException
   */
  public function testValidateRequestBadResponseType()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'foo',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadScope()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestNotExistsScope()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "badscope1",
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadState()
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
    $response_type = $this->app['oauth2.auth.response_type'];
    // This won't happened!!
    $this->assertTrue(is_object($response_type));
  }

  public function testValidateRequestGoodRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('http://democlient4.com/redirect_uri');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
      'redirect_uri' => 'http://democlient4.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));
  }

  public function testValidateRequestGoodResponseType()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));
  }

  public function testValidateRequestGoodScope()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));

    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));
  }

  public function testValidateRequestGoodState()
  {
    $request = new Request();
    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    ));
    $request->overrideGlobals();
    $response_type = $this->app['oauth2.auth.response_type'];
    $this->assertTrue(is_object($response_type));
  }
}
