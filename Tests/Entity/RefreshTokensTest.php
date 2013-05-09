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

use Pantarei\OAuth2\Tests\Entity\RefreshTokens;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokensTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $refreshTokenRepository = $this->em->getRepository('Pantarei\OAuth2\Tests\Entity\RefreshTokens');
    $refreshToken = $refreshTokenRepository->find(1);

    $this->assertTrue($refreshToken !== NULL);
    $this->assertEquals('288b5ea8e75d2b24368a79ed5ed9593b', $refreshToken->getRefreshToken());
    $this->assertEquals('http://democlient3.com/', $refreshToken->getClientId());
    $this->assertEquals('86400', $refreshToken->getExpiresIn());
    $this->assertEquals('demouser3', $refreshToken->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $refreshToken->getScope());
  }
}
