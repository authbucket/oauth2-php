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
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test password grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeTest extends OAuth2WebTestCase
{
  public function testGrantType()
  {
    $query = array(
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => 'demoscope1',
    );
    $grant_type = new PasswordGrantType($query, $query);
    $this->assertEquals('password', $grant_type->getGrantType());

    $grant_type->setUsername('demouser2');
    $this->assertEquals('demouser2', $grant_type->getUsername());

    $grant_type->setPassword('demopassword1');
    $this->assertEquals('demopassword1', $grant_type->getPassword());

    $grant_type->setScope('demoscope2');
    $this->assertEquals('demoscope2', $grant_type->getScope());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testNoUsername()
  {
    $query = array(
      'password' => 'demopassword1',
      'scope' => 'demoscope1',
    );
    $grant_type = new PasswordGrantType($query, $query);
    // This won't happened!!
    $this->assertEquals('password', $grant_type->getGrantType());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
   */
  public function testBadUsername()
  {
    $query = array(
      'username' => 'badusername1',
      'password' => 'demopassword1',
      'scope' => 'demoscope1',
    );
    $grant_type = new PasswordGrantType($query, $query);
    // This won't happened!!
    $this->assertEquals('password', $grant_type->getGrantType());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testNoPassword()
  {
    $query = array(
      'username' => 'demousername1',
      'scope' => 'demoscope1',
    );
    $grant_type = new PasswordGrantType($query, $query);
    // This won't happened!!
    $this->assertEquals('password', $grant_type->getGrantType());
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
   */
  public function testBadPassword()
  {
    $query = array(
      'username' => 'demousername1',
      'password' => 'badpassword1',
      'scope' => 'demoscope1',
    );
    $grant_type = new PasswordGrantType($query, $query);
    // This won't happened!!
    $this->assertEquals('password', $grant_type->getGrantType());
  }
}
