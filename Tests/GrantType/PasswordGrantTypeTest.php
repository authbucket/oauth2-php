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

use Pantarei\OAuth2\GrantType\PasswordGrantType;

/**
 * Test password grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGrantType()
  {
    $grant_type = new PasswordGrantType();
    $this->assertEquals('password', $grant_type->getGrantType());
  }

  public function testUsername()
  {
    $grant_type = new PasswordGrantType(array(
      'username' => 'edison',
    ));
    $this->assertEquals('edison', $grant_type->getUsername());

    $grant_type->setUsername('rebecca');
    $this->assertEquals('rebecca', $grant_type->getUsername());
  }

  public function testPassword()
  {
    $grant_type = new PasswordGrantType(array(
      'username' => 'edison',
      'password' => 'hello123',
    ));
    $this->assertEquals('hello123', $grant_type->getPassword());

    $grant_type->setPassword('abc123');
    $this->assertEquals('abc123', $grant_type->getPassword());
  }

  public function testScope()
  {
    $grant_type = new PasswordGrantType(array(
      'username' => 'edison',
      'password' => 'hello123',
      'scope' => 'aaa bbb ccc',
    ));
    $this->assertEquals('aaa bbb ccc', $grant_type->getScope());

    $grant_type->setScope('ddd eee fff');
    $this->assertEquals('ddd eee fff', $grant_type->getScope());
  }
}
