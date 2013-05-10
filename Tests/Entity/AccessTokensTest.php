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
use Pantarei\OAuth2\Tests\Entity\AccessTokens;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test access tokens entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokensTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $entity = Database::getConnection()->find('Pantarei\OAuth2\Tests\Entity\AccessTokens', 1);
    $this->assertEquals('Pantarei\OAuth2\Tests\Entity\AccessTokens', get_class($entity));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('3600', $entity->getExpiresIn());
    $this->assertEquals('demouser1', $entity->getUsername());
    $this->assertEquals(array('demoscope1'), $entity->getScope());
  }
}
