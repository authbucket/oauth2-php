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

use Pantarei\OAuth2\GrantType\ClientCredentialsGrantType;

/**
 * Test client credential grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientCredentialsGrantTypeTest extends \PHPUnit_Framework_TestCase
{
  public function testGrantType()
  {
    $grant_type = new ClientCredentialsGrantType();
    $this->assertEquals('client_credentials', $grant_type->getGrantType());
  }

  public function testScope()
  {
    $grant_type = new ClientCredentialsGrantType(array(
      'scope' => 'aaa bbb ccc',
    ));
    $this->assertEquals('aaa bbb ccc', $grant_type->getScope());

    $grant_type->setScope('ddd eee fff');
    $this->assertEquals('ddd eee fff', $grant_type->getScope());
  }
}
