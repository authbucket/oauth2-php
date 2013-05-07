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

use Pantarei\Oauth2\Tests\Entity\Authorizes;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizesTest extends Oauth2_Database_TestCase
{
  public function testFind()
  {
    $authorizeRepository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\Authorizes');
    $authorize = $authorizeRepository->find(3);

    $this->assertTrue($authorize !== NULL);
    $this->assertEquals('http://democlient3.com/', $authorize->getClientId());
    $this->assertEquals('demouser3', $authorize->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2', 'demoscope3'), $authorize->getScope());
  }
}
