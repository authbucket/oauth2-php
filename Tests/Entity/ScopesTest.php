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

use Pantarei\OAuth2\Tests\Entity\Scopes;
use Pantarei\OAuth2\Tests\OAuth2_Database_TestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ScopesTest extends OAuth2_Database_TestCase
{
  public function testFind()
  {
    $scopeRepository = $this->em->getRepository('Pantarei\OAuth2\Tests\Entity\Scopes');
    $scope = $scopeRepository->find(1);

    $this->assertTrue($scope !== NULL);
    $this->assertEquals('demoscope1', $scope->getScope());
  }
}
