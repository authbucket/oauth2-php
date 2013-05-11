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
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

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
    $request = new AuthorizationRequest();

    $query = array();
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoRedirectUri()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'client_id' => '1234',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoResponseType()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\UnsupportedResponseTypeException
   */
  public function testValidateRequestBadResponseType()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'foo',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect_uri',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodResponseType()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    );
    $response_type = $request->validateRequest($query);
    $this->assertEquals('Pantarei\\OAuth2\\ResponseType\\CodeResponseType', get_class($response_type));

    $query = array(
      'response_type' => 'token',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    );
    $response_type = $request->validateRequest($query);
    $this->assertEquals('Pantarei\\OAuth2\\ResponseType\\TokenResponseType', get_class($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadScope()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodScope()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
    );
    $response_type = $request->validateRequest($query);
    $this->assertEquals('Pantarei\\OAuth2\\ResponseType\\CodeResponseType', get_class($response_type));

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $response_type = $request->validateRequest($query);
    $this->assertEquals('Pantarei\\OAuth2\\ResponseType\\CodeResponseType', get_class($response_type));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadState()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => "aaa bbb ccc",
      'state' => "aaa\x19bbb\x7Fccc",
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodState()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1 demoscope2 demoscope3',
      'state' => 'example state',
    );
    $response_type = $request->validateRequest($query);
    $this->assertEquals('Pantarei\\OAuth2\\ResponseType\\CodeResponseType', get_class($response_type));
  }
}
