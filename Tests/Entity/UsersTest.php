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
use Pantarei\OAuth2\Entity\Users;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UsersTest extends OAuth2WebTestCase
{
  public function testAbstract()
  {
    $entity = new Users();
    $entity->setId(1)->setUsername('demousername1')
      ->setPassword('demopassword1');
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('demousername1', $entity->getUsername());
    $this->assertEquals('demopassword1', $entity->getPassword());
  }

  public function testFind()
  {
    $entity = Database::find('Users', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\Users', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('demousername1', $entity->getUsername());
    $this->assertEquals('demopassword1', $entity->getPassword());
  }
}
