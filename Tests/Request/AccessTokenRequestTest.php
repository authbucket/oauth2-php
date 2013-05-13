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

use Pantarei\OAuth2\Request\AccessTokenRequest;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequestTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->get('/validaterequest', function(Request $request) {
      $request->overrideGlobals();
      $response = new Response();
      $controller = new AccessTokenRequest();

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
  public function testValidateRequestNoGrantType()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array();
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }
  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
   */
  public function testValidateRequestBadGrantType()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'foo',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testValidateRequestClientBothEmpty()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestClientBothExists()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'client_id' => 'http://democlient1.com/',
      'client_secret' => 'demosecret1',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testValidateRequestClientBadBasic()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://badclient1.com/',
      'PHP_AUTH_PW' => 'badsecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestClientGoodBasic()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testValideRequestClientBadPost()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'client_id' => 'http://badclient1.com/',
      'client_secret' => 'badsecret1',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValideRequestClientGoodPost()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
      'client_id' => 'http://democlient2.com/',
      'client_secret' => 'demosecret2',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadAuthCodeNoCode()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodAuthCodeNoRedirectUri()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodAuthCode()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadClientCredBadState()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'client_credentials',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodClientCred()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'client_credentials',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadPasswordNoUsername()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'password',
      'password' => 'demopassword1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadPasswordNoPassword()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadPasswordBadState()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodPassword()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadRefreshTokenNoToken()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'refresh_token',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadRefreshTokenBadState()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => "demoscope1\x22demoscope2\x5cemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    // This won't happened!!
    $this->assertTrue(is_object($filtered_query));
  }

  public function testValidateRequestGoodRefreshToken()
  {
    $controller = new AccessTokenRequest();
    $request = new Request();

    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $filtered_query = $controller->validateRequest();
    $this->assertTrue(is_object($filtered_query));
  }
}
