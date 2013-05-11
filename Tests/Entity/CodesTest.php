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
use Pantarei\OAuth2\Entity\Codes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodesTest extends OAuth2_Database_TestCase
{
  public function testAbstract()
  {
    $entity = new Codes();
    $entity->setId(1)
      ->setCode('f0c68d250bcc729eb780a235371a9a55')
      ->setClientId('http://democlient2.com/')
      ->setRedirectUri('http://democlient2.com/redirect_uri')
      ->setExpiresIn('300')
      ->setUsername('demouser2')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
      ));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('f0c68d250bcc729eb780a235371a9a55', $entity->getCode());
    $this->assertEquals('http://democlient2.com/', $entity->getClientId());
    $this->assertEquals('http://democlient2.com/redirect_uri', $entity->getRedirectUri());
    $this->assertEquals('300', $entity->getExpiresIn());
    $this->assertEquals('demouser2', $entity->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2'), $entity->getScope());
  }

  public function testFind()
  {
    $entity = Database::find('Codes', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\Codes', get_class($entity));
    $this->assertEquals(1, $entity->getId());
    $this->assertEquals('f0c68d250bcc729eb780a235371a9a55', $entity->getCode());
    $this->assertEquals('http://democlient2.com/', $entity->getClientId());
    $this->assertEquals('http://democlient2.com/redirect_uri', $entity->getRedirectUri());
    $this->assertEquals('300', $entity->getExpiresIn());
    $this->assertEquals('demouser2', $entity->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2'), $entity->getScope());
  }
}
