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
use Pantarei\OAuth2\Entity\AccessTokens;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test access tokens entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokensTest extends OAuth2WebTestCase
{
  public function testAbstract()
  {
    $entity = new AccessTokens();
    $entity->setId(1)
      ->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
      ->setClientId('http://democlient1.com/')
      ->setExpiresIn('3600')
      ->setUsername('demouser1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('3600', $entity->getExpiresIn());
    $this->assertEquals('demouser1', $entity->getUsername());
    $this->assertEquals(array('demoscope1'), $entity->getScope());
  }

  public function testFind()
  {
    $entity = Database::find('AccessTokens', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\AccessTokens', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('3600', $entity->getExpiresIn());
    $this->assertEquals('demouser1', $entity->getUsername());
    $this->assertEquals(array('demoscope1'), $entity->getScope());
  }
}
