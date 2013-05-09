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

use Pantarei\OAuth2\Tests\Entity\Users;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UsersTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $userRepository = $this->em->getRepository('Pantarei\OAuth2\Tests\Entity\Users');
    $user = $userRepository->find(1);

    $this->assertTrue($user !== NULL);
    $this->assertEquals('demouser1', $user->getUsername());
    $this->assertEquals('demopassword1', $user->getPassword());
  }
}
