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
use Pantarei\OAuth2\Entity\Scopes;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test base OAuth2.0 exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DatabaseTest extends OAuth2WebTestCase
{

  public function testFind()
  {
    $result = Database::find('AccessTokens', 1);
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
  }

  public function testFindBy()
  {
    $result = Database::findBy('AccessTokens', array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result[0]->getAccessToken());
  }

  public function testFindOneBy()
  {
    $result = Database::findOneBy('AccessTokens', array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
  }

  public function testFindAll()
  {
    $result = Database::findAll('Clients');
    $this->assertEquals(3, count($result));
  }

  public function testPersist()
  {
    $data = new Scopes();
    $data->setScope('demoscope4');
    Database::getconnection()->persist($data);

    $result = Database::findAll('Scopes');
    $this->assertEquals(4, count($result));
    $this->assertEquals('demoscope4', $result[3]->getScope());
  }

  public function testRemove()
  {
    $data = new Scopes();
    $data->setScope('demoscope4');
    Database::getconnection()->persist($data);

    $result = Database::findAll('Scopes');
    $this->assertEquals(4, count($result));
    $this->assertEquals('demoscope4', $result[3]->getScope());

    Database::remove($data);
    $result = Database::findAll('Scopes');
    $this->assertEquals(3, count($result));
  }
}
