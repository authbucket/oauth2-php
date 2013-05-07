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

use Pantarei\Oauth2\Tests\Entity\Codes;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodesTest extends Oauth2_Database_TestCase
{
  public function testFind()
  {
    $codeRepository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\Codes');
    $code = $codeRepository->find(1);

    $this->assertTrue($code !== NULL);
    $this->assertEquals('f0c68d250bcc729eb780a235371a9a55', $code->getCode());
    $this->assertEquals('http://democlient2.com/', $code->getClientId());
    $this->assertEquals('http://democlient2.com/redirect', $code->getRedirectUri());
    $this->assertEquals('300', $code->getExpiresIn());
    $this->assertEquals('demouser2', $code->getUsername());
    $this->assertEquals(array('demoscope1', 'demoscope2'), $code->getScope());
  }
}
