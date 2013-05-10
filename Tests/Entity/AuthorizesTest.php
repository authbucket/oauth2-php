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
use Pantarei\OAuth2\Tests\Entity\Authorizes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizesTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $entity = Database::getConnection()->find('Pantarei\OAuth2\Tests\Entity\Authorizes', 3);
    $this->assertEquals('Pantarei\OAuth2\Tests\Entity\Authorizes', get_class($entity));
    $this->assertEquals('http://democlient3.com/', $entity->getClientId());
    $this->assertEquals('demouser3', $entity->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $entity->getScope());
  }
}
