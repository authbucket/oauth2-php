<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Test\Request;

use Pantarei\Oauth2\Request\AccessTokenRequest;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequestTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoAccessToken()
  {
    $request = new AccessTokenRequest();

    $query = array();
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoTokenType()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadTokenType()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'foo',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodTokenType()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));

    $query = array(
      'access_token' => '1234',
      'token_type' => 'mac',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadExpiresIn()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => 'bad expires in',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodExpiresIn()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\Oauth2\Exception\InvalidScopeException
   */
  public function testValidateRequestBadScope()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
      'scope' => "aaa\x22bbb\x5Cccc\x7Fddd",
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodScope()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
      'scope' => 'aaa',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
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
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
      'scope' => 'aaa bbb ccc',
      'state' => "aaa\x19bbb\x7Fccc",
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  public function testValidateRequestGoodState()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'access_token' => '1234',
      'token_type' => 'bearer',
      'expires_in' => '86400',
      'scope' => 'aaa bbb ccc',
      'state' => 'example state',
    );
    $filtered_query = $request->validateRequest($query);
    $this->assertTrue(is_array($filtered_query));
  }
}
