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

use Pantarei\OAuth2\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenServiceProviderTest extends OAuth2WebTestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testNoGrantType()
  {
    $request = new Request();
    $post = array();
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token.options.initializer']());
    $this->assertFalse($this->app['oauth2.token.options.initializer']());
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }
  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedGrantTypeException
   */
  public function testBadGrantType()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'foo',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testClientBothEmpty()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testClientBothExists()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testClientBadBasic()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testClientGoodBasic()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testValideRequestClientBadPost()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'client_id' => 'http://badclient1.com/',
      'client_secret' => 'badsecret1',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testValideRequestClientGoodPost()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadAuthCodeNoCode()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testGoodAuthCodeNoRedirectUri()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }

  public function testGoodAuthCode()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadClientCredBadState()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testGoodClientCred()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadPasswordNoUsername()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadPasswordNoPassword()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadPasswordBadState()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testGoodPassword()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadRefreshTokenNoToken()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadRefreshTokenBadState()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    // This won't happened!!
    $this->assertTrue(is_object($grant_type));
  }

  public function testGoodRefreshToken()
  {
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
    $grant_type = $this->app['oauth2.token.grant_type'];
    $this->assertTrue(is_object($grant_type));
  }
}
