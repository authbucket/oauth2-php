<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Test\GrantType;

use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantType;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGrantType()
  {
    $grant_type = new AuthorizationCodeGrantType();
    $this->assertEquals('authorization_code', $grant_type->getGrantType());
  }

  public function testCode()
  {
    $grant_type = new AuthorizationCodeGrantType(array(
      'code' => 'abcd',
    ));
    $this->assertEquals('abcd', $grant_type->getCode());

    $grant_type->setCode('efgh');
    $this->assertEquals('efgh', $grant_type->getCode());
  }

  public function testRedirectUri()
  {
    $grant_type = new AuthorizationCodeGrantType(array(
      'code' => 'abcd',
      'redirect_uri' => 'http://example.com/redirect',
    ));
    $this->assertEquals('http://example.com/redirect', $grant_type->getRedirectUri());

    $grant_type->setRedirectUri('http://abc.com/redirect');
    $this->assertEquals('http://abc.com/redirect', $grant_type->getRedirectUri());
  }

  public function testClientId()
  {
    $grant_type = new AuthorizationCodeGrantType(array(
      'code' => 'abcd',
      'redirect_uri' => 'http://example.com/redirect',
      'client_id' => '1234',
    ));
    $this->assertEquals('1234', $grant_type->getClientId());

    $grant_type->setClientId('5678');
    $this->assertEquals('5678', $grant_type->getClientId());
  }
}
