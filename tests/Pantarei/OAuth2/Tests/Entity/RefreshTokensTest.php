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

use Pantarei\OAuth2\Entity\RefreshTokens;
use Pantarei\OAuth2\OAuth2WebTestCase;

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
    $data->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
      ->setClientId('http://democlient3.com/')
      ->setExpires(time() + 86400)
      ->setUsername('demousername3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $data->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $data->getClientId());
    $this->assertTrue($data->getExpires() > time());
    $this->assertEquals('demousername3', $data->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $data->getScope());
  }

  public function testFind()
  {
    $result = $this->app['oauth2.orm']->find('Pantarei\OAuth2\Entity\RefreshTokens', 1);
    $this->assertEquals('Pantarei\OAuth2\Entity\RefreshTokens', get_class($result));
    $this->assertEquals(1, $result->getId());
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $result->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $result->getClientId());
    $this->assertTrue($result->getExpires() > time());
    $this->assertEquals('demousername3', $result->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $result->getScope());
  }

  public function testExpired()
  {
    $data = new RefreshTokens();
    $data->setRefreshToken('5ddaa68ac1805e728563dd7915441408')
      ->setClientId('http://democlient4.com/')
      ->setExpires(time() - 3600)
      ->setUsername('demousername4')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['oauth2.orm']->persist($data);
    $this->app['oauth2.orm']->flush();

    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\RefreshTokens')->findOneBy(array(
      'refresh_token' => '5ddaa68ac1805e728563dd7915441408',
    ));
    $this->assertTrue($result !== NULL);
    $this->assertTrue($result->getExpires() < time());
  }
}
