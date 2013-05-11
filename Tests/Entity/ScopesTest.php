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
use Pantarei\OAuth2\Entity\Scopes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ScopesTest extends OAuth2_Database_TestCase
{
  public function testAbstract()
  {
    $entity = new Scopes();
    $entity->setId(1)
      ->setScope('demoscope1');
    $this->assertEquals(1, $entity->getId());
    $this->assertTrue($entity !== NULL);
    $this->assertEquals('demoscope1', $entity->getScope());
  }

  public function testFind()
  {
    $entity = Database::find('Scopes', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\Scopes', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertTrue($entity !== NULL);
    $this->assertEquals('demoscope1', $entity->getScope());
  }
}
