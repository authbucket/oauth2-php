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

/**
 * Test authorization code grant type functionality
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGetGrantType()
  {
    $grant_type = new \Pantarei\Oauth2\GrantType\AuthorizationCodeGrantType();
    $this->assertEquals('authorization_code', $grant_type->getGrantType());
  }
}
