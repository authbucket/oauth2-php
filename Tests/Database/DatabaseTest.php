<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Database;

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Tests\Entity\Scopes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test base OAuth2.0 exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DatabaseTest extends OAuth2_Database_TestCase
{

  public function testFind()
  {
    $entity = Database::getConnection()->find('Pantarei\OAuth2\Tests\Entity\AccessTokens', 1);
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity->getAccessToken());
  }

  public function testFindBy()
  {
    $entity = Database::getConnection()->findBy('Pantarei\OAuth2\Tests\Entity\AccessTokens', array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity[0]->getAccessToken());
  }

  public function testFindOneBy()
  {
    $entity = Database::getConnection()->findOneBy('Pantarei\OAuth2\Tests\Entity\AccessTokens', array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $entity->getAccessToken());
  }

  public function testFindAll()
  {
    $entities = Database::getConnection()->findAll('Pantarei\OAuth2\Tests\Entity\Clients');
    $this->assertEquals(3, count($entities));
  }

  public function testPersist()
  {
    $entity = new Scopes();
    $entity->setScope('demoscope4');
    Database::getconnection()->persist($entity);

    $entities = Database::getConnection()->findAll('Pantarei\OAuth2\Tests\Entity\Scopes');
    $this->assertEquals(4, count($entities));
    $this->assertEquals('demoscope4', $entities[3]->getScope());
  }

  public function testRemove()
  {
    $entity = new Scopes();
    $entity->setScope('demoscope4');
    Database::getconnection()->persist($entity);

    $entities = Database::getConnection()->findAll('Pantarei\OAuth2\Tests\Entity\Scopes');
    $this->assertEquals(4, count($entities));
    $this->assertEquals('demoscope4', $entities[3]->getScope());

    Database::getConnection()->remove($entity);
    $entities = Database::getConnection()->findAll('Pantarei\OAuth2\Tests\Entity\Scopes');
    $this->assertEquals(3, count($entities));
  }
}
