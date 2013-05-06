<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Test\GrantType;

use Pantarei\Oauth2\GrantType\RefreshTokenGrantType;

/**
 * Test refresh token grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGrantType()
  {
    $grant_type = new RefreshTokenGrantType();
    $this->assertEquals('refresh_token', $grant_type->getGrantType());
  }

  public function testRefreshToken()
  {
    $grant_type = new RefreshTokenGrantType(array(
      'refresh_token' => 'abcd',
    ));
    $this->assertEquals('abcd', $grant_type->getRefreshToken());

    $grant_type->setRefreshToken('efgh');
    $this->assertEquals('efgh', $grant_type->getRefreshToken());
  }

  public function testScope()
  {
    $grant_type = new RefreshTokenGrantType(array(
      'refresh_token' => 'abcd',
      'scope' => 'aaa bbb ccc',
    ));
    $this->assertEquals('aaa bbb ccc', $grant_type->getScope());

    $grant_type->setScope('ddd eee fff');
    $this->assertEquals('ddd eee fff', $grant_type->getScope());
  }
}
