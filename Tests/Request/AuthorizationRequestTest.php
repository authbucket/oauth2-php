<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Test\Request;

use Pantarei\Oauth2\Request\AuthorizationRequest;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequestTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
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
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
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
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoResponseType()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\UnsupportedResponseTypeException
   */
  public function testValidateRequestBadResponseType()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'foo',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
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
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));

    $query = array(
      'response_type' => 'token',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadScope()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
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
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
      'scope' => 'aaa',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));

    $query = array(
      'response_type' => 'code',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
      'scope' => 'aaa bbb ccc',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadState()
  {
    $request = new AuthorizationRequest();

    $query = array(
      'response_type' => 'code',
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
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
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
      'scope' => 'aaa bbb ccc',
      'state' => 'example state',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }
}
