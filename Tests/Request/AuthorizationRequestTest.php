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

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Request\AuthorizationRequest;
use Pantarei\OAuth2\Tests\Entity\Clients;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequestTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->get('/validaterequest', function(Request $request) {
      $request->overrideGlobals();
      $response = new Response();
      $controller = new AuthorizationRequest();

      $response_type = $controller->validateRequest();
      return (is_object($response_type))
        ? $response->setStatusCode(200)
        : $response->setStatusCode(404);
    });

    return $app;
  }

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
    $filtered_query = $controller->validateRequest();
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
    $response_type = $controller->validateRequest();
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
    $filtered_query = $controller->validateRequest();
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
    Database::persist($client);

    $controller = new AuthorizationRequest();
    $request = new Request();

    $request->initialize(array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
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
    $filtered_query = $controller->validateRequest();
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
    $filtered_query = $controller->validateRequest();
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
    $filtered_query = $controller->validateRequest();
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
    $filtered_query = $controller->validateRequest();
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
      'scope' => "aaa bbb ccc",
      'state' => "aaa\x19bbb\x7Fccc",
    ));
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
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
    Database::persist($client);

    // It works even if we skip redirect_uri from GET.
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());

    // And for sure, if match redirect_uri from GET it works, too.
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient4.com/',
      'redirect_uri' => 'http://democlient4.com/redirect_uri',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());
  }

  public function testValidateRequestGoodResponseType()
  {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());

    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());
  }

  public function testValidateRequestGoodScope()
  {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());

    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());
  }

  public function testValidateRequestGoodState()
  {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());
  }
}
