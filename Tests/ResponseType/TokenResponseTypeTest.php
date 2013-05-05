<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Test\ResponseType;

use Pantarei\Oauth2\ResponseType\TokenResponseType;

/**
 * Test token response type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testResponseType()
  {
    $grant_type = new TokenResponseType();
    $this->assertEquals('token', $grant_type->getResponseType());
  }

  public function testClientId()
  {
    $response_type = new TokenResponseType(array(
      'client_id' => '1234',
    ));
    $this->assertEquals('1234', $response_type->getClientId());

    $response_type->setClientId('5678');
    $this->assertEquals('5678', $response_type->getClientId());
  }

  public function testRedirectUri()
  {
    $response_type = new TokenResponseType(array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
    ));
    $this->assertEquals('http://example.com/redirect', $response_type->getRedirectUri());

    $response_type->setRedirectUri('http://abc.com/redirect');
    $this->assertEquals('http://abc.com/redirect', $response_type->getRedirectUri());
  }

  public function testScope()
  {
    $response_type = new TokenResponseType(array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
      'scope' => 'aaa bbb ccc',
    ));
    $this->assertEquals('aaa bbb ccc', $response_type->getScope());

    $response_type->setScope('ddd eee fff');
    $this->assertEquals('ddd eee fff', $response_type->getScope());
  }

  public function testState()
  {
    $response_type = new TokenResponseType(array(
      'client_id' => '1234',
      'redirect_uri' => 'http://example.com/redirect',
      'scope' => 'aaa bbb ccc',
      'state' => 'demo state',
    ));
    $this->assertEquals('demo state', $response_type->getState());

    $response_type->setState('example state');
    $this->assertEquals('example state', $response_type->getState());
  }

}
