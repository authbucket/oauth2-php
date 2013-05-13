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
    $data = new AccessTokens();
    $data->setId(1)
      ->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
      ->setClientId('http://democlient1.com/')
      ->setExpiresIn('3600')
      ->setUsername('demouser1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->assertEquals(1, $data->getId());
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $data->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $data->getClientId());
    $this->assertEquals('3600', $data->getExpiresIn());
    $this->assertEquals('demouser1', $data->getUsername());
    $this->assertEquals(array('demoscope1'), $data->getScope());
  }

  public function testFind()
  {
    $result = Database::find('AccessTokens', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\AccessTokens', get_class($result));
    $this->assertEquals(1, $result->getId());
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $result->getClientId());
    $this->assertEquals('3600', $result->getExpiresIn());
    $this->assertEquals('demouser1', $result->getUsername());
    $this->assertEquals(array('demoscope1'), $result->getScope());
  }
}
