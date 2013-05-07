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

use Pantarei\Oauth2\Tests\Entity\Scopes;
use Pantarei\Oauth2\Tests\Oauth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ScopesTest extends Oauth2_Database_TestCase
{
  public function testFind()
  {
    $scopeRepository = $this->em->getRepository('Pantarei\Oauth2\Tests\Entity\Scopes');
    $scope = $scopeRepository->find(1);

    $this->assertTrue($scope !== NULL);
    $this->assertEquals('demoscope1', $scope->getScope());
  }
}
