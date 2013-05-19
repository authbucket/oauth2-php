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

use Pantarei\OAuth2\Entity\Codes;
use Pantarei\OAuth2\Entity\Clients;
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
  public function testExceptionNoGrantType()
  {
    $request = new Request();
    $post = array();
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }
  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedGrantTypeException
   */
  public function testExceptionBadGrantType()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'foo',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionAuthCodeNoClientId()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionAuthCodeBothClientId()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testExceptionAuthCodeBadBasicClientId()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
   */
  public function testExceptionAuthCodeBadPostClientId()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionAuthCodeNoCode()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionAuthCodeNoSavedNoPassedRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $code = new Codes();
    $code->setCode('08fb55e26c84f8cb060b7803bc177af8')
      ->setClientId('http://democlient4.com/')
      ->setRedirectUri('')
      ->setExpires(time() + 3600)
      ->setUsername('demousername4')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['oauth2.orm']->persist($code);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'code' => '08fb55e26c84f8cb060b7803bc177af8',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient4.com/',
      'PHP_AUTH_PW' => 'demosecret4',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionAuthCodeBadRedirectUri()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/wrong_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testExceptionClientCredBadState()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'client_credentials',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionClientCredBadStateFormat()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionPasswordNoUsername()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionPasswordNoPassword()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testExceptionPasswordBadScope()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionPasswordBadScopeFormat()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionRefreshTokenNoToken()
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
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testExceptionRefreshTokenBadScope()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient3.com/',
      'PHP_AUTH_PW' => 'demosecret3',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testExceptionRefreshTokenBadScopeFormat()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    // This won't happened!!
    $this->assertTrue($this->app['oauth2.token']);
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
    $this->assertTrue($this->app['oauth2.token']);

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
    $this->assertTrue($this->app['oauth2.token']);
  }

  public function testGoodAuthCodeNoPassedRedirectUri()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'client_id' => 'http://democlient2.com/',
      'client_secret' => 'demosecret2',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token']);
  }

  public function testGoodAuthCodeNoStoredRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $code = new Codes();
    $code->setCode('08fb55e26c84f8cb060b7803bc177af8')
      ->setClientId('http://democlient4.com/')
      ->setRedirectUri('')
      ->setExpires(time() + 3600)
      ->setUsername('demousername4')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['oauth2.orm']->persist($code);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'code' => '08fb55e26c84f8cb060b7803bc177af8',
      'redirect_uri' => 'http://democlient4.com/redirect_uri',
      'client_id' => 'http://democlient4.com/',
      'client_secret' => 'demosecret4',
    );
    $server = array();
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token']);
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
    $this->assertTrue($this->app['oauth2.token']);
  }

  public function testGoodPassword()
  {
    $request = new Request();
    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'demostate1',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token']);
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
      'PHP_AUTH_USER' => 'http://democlient3.com/',
      'PHP_AUTH_PW' => 'demosecret3',
    );
    $request->initialize(array(), $post, array(), array(), array(), $server);
    $request->overrideGlobals();
    $this->assertTrue($this->app['oauth2.token']);
  }
}
