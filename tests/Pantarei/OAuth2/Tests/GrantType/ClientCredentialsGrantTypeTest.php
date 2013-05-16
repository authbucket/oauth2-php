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
use Pantarei\OAuth2\OAuth2WebTestCase;

/**
 * Test client credential grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientCredentialsGrantTypeTest extends OAuth2WebTestCase
{
  public function testGrantType()
  {
    $query = array(
      'scope' => 'demoscope1',
    );
    $grant_type = new ClientCredentialsGrantType($this->app, $query, $query);
    $this->assertEquals('client_credentials', $grant_type->getGrantType());

    $grant_type->setScope('demoscope2');
    $this->assertEquals('demoscope2', $grant_type->getScope());
  }
}
