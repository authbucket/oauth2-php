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
use Pantarei\OAuth2\Entity\Authorizes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizesTest extends OAuth2_Database_TestCase
{
  public function testAbstract()
  {
    $entity = new Authorizes();
    $entity->setId(1)
      ->setClientId('http://democlient1.com/')
      ->setUsername('demouser1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('demouser1', $entity->getUsername());
    $this->assertEquals(array('demoscope1'), $entity->getScope());
  }

  public function testFind()
  {
    $entity = Database::find('Authorizes', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\Authorizes', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('demouser1', $entity->getUsername());
    $this->assertEquals(array('demoscope1'), $entity->getScope());
  }
}
