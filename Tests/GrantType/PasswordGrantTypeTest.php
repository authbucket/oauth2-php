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

use Pantarei\Oauth2\GrantType\PasswordGrantType;

/**
 * Test password grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGetGrantType()
  {
    $grant_type = new PasswordGrantType();
    $this->assertEquals('password', $grant_type->getGrantType());
  }
}
