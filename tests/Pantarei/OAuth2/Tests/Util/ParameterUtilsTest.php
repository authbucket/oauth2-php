<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Util;

use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Entity\RefreshTokens;
use Pantarei\OAuth2\Entity\Scopes;
use Pantarei\OAuth2\OAuth2WebTestCase;
use Pantarei\OAuth2\Util\ParameterUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Testing parameter utility functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ParameterUtilsTest extends OAuth2WebTestCase
{
  public function testFilter()
  {
    $array = array(
      'client_id' => 'http://democlient1.com/',
      'client_secret' => 'demosecret1',
    );
    $filtered_array = ParameterUtils::filter($array);
    $this->assertEquals($array, $filtered_array);

    $array = array(
      'client_id' => 'http://democlient1.com/',
      'client_secret' => 'demosecret1',
    );
    $params = array('client_id');
    $filtered_array = ParameterUtils::filter($array, $params);
    $this->assertEquals(1, count($filtered_array));
    $this->assertEquals('http://democlient1.com/', $filtered_array['client_id']);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testBadCheckClientIdEmpty()
  {
    $request = new Request();
    $get = array();
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'GET');
    // This won't happened!!
    $this->assertTrue($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testBadCheckClientIdBadGet()
  {
    $request = new Request();
    $get = array(
      'client_id' => 'http://badclient1.com/',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'GET');
    // This won't happened!!
    $this->assertTrue($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testBadCheckClientIdBadPost()
  {
    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://badclient1.com/',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'POST');
    // This won't happened!!
    $this->assertTrue($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public function testBadCheckClientIdBadServer()
  {
    $request = new Request();
    $get = array();
    $post = array();
    $server = array(
      'PHP_AUTH_USER' => 'http://badclient1.com/',
    );
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'POST');
    // This won't happened!!
    $this->assertTrue($result);
  }

  public function testGoodCheckClientId()
  {
    $request = new Request();
    $get = array(
      'client_id' => 'http://democlient1.com/',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'GET');
    $this->assertEquals($get['client_id'], $result);

    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://democlient1.com/',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'POST');
    $this->assertEquals($post['client_id'], $result);

    $request = new Request();
    $get = array();
    $post = array();
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
    );
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkClientId($request, $this->app, 'POST');
    $this->assertEquals($server['PHP_AUTH_USER'], $result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testBadCheckScopeBadGet()
  {
    $request = new Request();
    $get = array(
      'scope' => 'demoscope1 badscope2 badscope3',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScope($request, $this->app, 'GET');
    // This won't happened!!
    $this->assertTrue($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testBadCheckScopeBadPost()
  {
    $request = new Request();
    $get = array();
    $post = array(
      'scope' => 'demoscope1 badscope2 badscope3',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScope($request, $this->app, 'POST');
    // This won't happened!!
    $this->assertTrue($result);
  }

  public function testGoodCheckScope()
  {
    $request = new Request();
    $get = array(
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScope($request, $this->app, 'GET');
    $this->assertTrue($result == array('demoscope1', 'demoscope2', 'demoscope3'));

    $request = new Request();
    $get = array();
    $post = array(
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScope($request, $this->app, 'POST');
    $this->assertTrue($result == array('demoscope1', 'demoscope2', 'demoscope3'));
  }

  public function testCheckScopeByCode()
  {
    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://democlient2.com/',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScopeByCode($request, $this->app);
    $this->assertTrue($result == array('demoscope1', 'demoscope2'));

    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://badclient2.com/',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScopeByCode($request, $this->app);
    $this->assertFalse($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testBadCheckScopeByRefreshTokenNotSubset()
  {
    $scope = new Scopes();
    $scope->setScope('demoscope4');
    $this->app['oauth2.orm']->persist($scope);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://democlient3.com/',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => 'demoscope1 demoscope2 demoscope3 demoscope4',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScopeByRefreshToken($request, $this->app);
    // This wont' happened!!
    $this->assertTrue($result);
  }

  public function testGoodCheckScopeByRefreshToken()
  {
    $refresh_token = new RefreshTokens();
    $refresh_token->setRefreshToken('13dcf9db36152fa322daf9deb7b0a22e')
      ->setTokenType('bearer')
      ->setClientId('http://democlient1.com/')
      ->setExpires(time() + 86400)
      ->setUsername('demousername1');
    $this->app['oauth2.orm']->persist($refresh_token);

    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://democlient1.com/',
      'refresh_token' => '13dcf9db36152fa322daf9deb7b0a22e',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScopeByRefreshToken($request, $this->app);
    $this->assertFalse($result);

    $request = new Request();
    $get = array();
    $post = array(
      'client_id' => 'http://democlient3.com/',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
    );
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkScopeByRefreshToken($request, $this->app);
    $this->assertTrue($result == array('demoscope1', 'demoscope2', 'demoscope3'));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadCheckRedirectUriEmpty()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $request = new Request();
    $get = array(
      'client_id' => 'http://democlient4.com/',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkRedirectUri($request, $this->app, 'GET');
    // This won't happened!!
    $this->assertTrue($result);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testBadCheckRedirectUriMismatch()
  {
    $request = new Request();
    $get = array(
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/wrong_uri',
    );
    $post = array();
    $server = array();
    $request->initialize($get, $post, array(), array(), array(), $server);
    $result = ParameterUtils::checkRedirectUri($request, $this->app, 'GET');
    // This won't happened!!
    $this->assertTrue($result);
  }
}
