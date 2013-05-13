<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\GrantType;

use Pantarei\OAuth2\GrantType\RefreshTokenGrantType;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test refresh token grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeTest extends OAuth2WebTestCase
{
  public function testGrantType()
  {
    $query = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => 'demoscope1',
    );
    $grant_type = new RefreshTokenGrantType($query, $query);
    $this->assertEquals('refresh_token', $grant_type->getGrantType());

    $grant_type->setRefreshToken('37ed55a16777958a3953088576869ca7');
    $this->assertEquals('37ed55a16777958a3953088576869ca7', $grant_type->getRefreshToken());

    $grant_type->setScope('demoscope2');
    $this->assertEquals('demoscope2', $grant_type->getScope());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testNoRefreshToken()
  {
    $query = array(
      'grant_type' => 'refresh_token',
      'scope' => 'demoscope1',
    );
    $grant_type = new RefreshTokenGrantType($query, $query);
    $this->assertEquals('refresh_token', $grant_type->getGrantType());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
   */
  public function testBadRefreshToken()
  {
    $query = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '37ed55a16777958a3953088576869ca7',
      'scope' => 'demoscope1',
    );
    $grant_type = new RefreshTokenGrantType($query, $query);
    $this->assertEquals('refresh_token', $grant_type->getGrantType());
  }
}
