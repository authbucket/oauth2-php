<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Entity;

use Pantarei\Oauth2\Tests\Entity\AccessTokens;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test access tokens entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokensTest extends Oauth2_Database_TestCase
{
  public function testFind()
  {
    $accessTokenRepository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\AccessTokens');
    $accessToken = $accessTokenRepository->find(1);

    $this->assertTrue($accessToken !== NULL);
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $accessToken->getAccessToken());
    $this->assertEquals('http://democlient1.com/', $accessToken->getClientId());
    $this->assertEquals('3600', $accessToken->getExpiresIn());
    $this->assertEquals('demouser1', $accessToken->getUsername());
    $this->assertEquals(array('demoscope1'), $accessToken->getScope());
  }
}
