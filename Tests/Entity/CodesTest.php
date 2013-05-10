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
use Pantarei\OAuth2\Tests\Entity\Codes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodesTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $entity = Database::getConnection()->find('Pantarei\OAuth2\Tests\Entity\Codes', 1);
    $this->assertEquals('Pantarei\OAuth2\Tests\Entity\Codes', get_class($entity));
    $this->assertEquals('f0c68d250bcc729eb780a235371a9a55', $entity->getCode());
    $this->assertEquals('http://democlient2.com/', $entity->getClientId());
    $this->assertEquals('http://democlient2.com/redirect', $entity->getRedirectUri());
    $this->assertEquals('300', $entity->getExpiresIn());
    $this->assertEquals('demouser2', $entity->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2'), $entity->getScope());
  }
}
