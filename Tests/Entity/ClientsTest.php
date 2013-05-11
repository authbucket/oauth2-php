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
use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientsTest extends OAuth2WebTestCase
{

  public function testAbstract()
  {
    $entity = new Clients();
    $entity->setId(1)
      ->setClientId('http://democlient1.com/')
      ->setClientSecret('demosecret1')
      ->setRedirectUri('http://democlient1.com/redirect_uri');
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('demosecret1', $entity->getClientSecret());
    $this->assertEquals('http://democlient1.com/redirect_uri', $entity->getRedirectUri());
  }

  public function testFind()
  {
    $entity = Database::find('Clients', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\Clients', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('http://democlient1.com/', $entity->getClientId());
    $this->assertEquals('demosecret1', $entity->getClientSecret());
    $this->assertEquals('http://democlient1.com/redirect_uri', $entity->getRedirectUri());
  }
}
