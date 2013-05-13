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
use Pantarei\OAuth2\Entity\RefreshTokens;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokensTest extends OAuth2WebTestCase
{
  public function testAbstract()
  {
    $data = new RefreshTokens();
    $data->setId(1)
      ->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
      ->setClientId('http://democlient3.com/')
      ->setExpiresIn('86400')
      ->setUsername('demouser3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->assertEquals(1, $data->getId());
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $data->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $data->getClientId());
    $this->assertEquals('86400', $data->getExpiresIn());
    $this->assertEquals('demouser3', $data->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $data->getScope());
  }

  public function testFind()
  {
    $result = Database::find('RefreshTokens', 1);
    $this->assertEquals('Pantarei\\OAuth2\\Tests\\Entity\\RefreshTokens', get_class($result));
    $this->assertEquals(1, $result->getId());
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $result->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $result->getClientId());
    $this->assertEquals('86400', $result->getExpiresIn());
    $this->assertEquals('demouser3', $result->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $result->getScope());
  }
}
