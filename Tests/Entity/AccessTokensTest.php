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
use Pantarei\Oauth2\Tests\Entity\AccessTokensRepository;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test access tokens entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokensTest extends Oauth2_Database_TestCase
{
  public function testInterface()
  {
    $token = new AccessTokens();

    $token_repository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\AccessTokens');
    $tokens = $token_repository->findAll();
  }
}
