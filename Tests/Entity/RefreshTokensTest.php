<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Entity;

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Tests\Entity\RefreshTokens;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokensTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $entity = Database::find('Pantarei\OAuth2\Tests\Entity\RefreshTokens', 1);
    $this->assertEquals('Pantarei\OAuth2\Tests\Entity\RefreshTokens', get_class($entity));
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $entity->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $entity->getClientId());
    $this->assertEquals('86400', $entity->getExpiresIn());
    $this->assertEquals('demouser3', $entity->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $entity->getScope());
  }
}
