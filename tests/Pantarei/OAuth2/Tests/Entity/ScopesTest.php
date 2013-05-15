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

use Pantarei\OAuth2\Entity\Scopes;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ScopesTest extends OAuth2WebTestCase
{
  public function testAbstract()
  {
    $data = new Scopes();
    $data->setScope('demoscope1');
    $this->assertTrue($data !== NULL);
    $this->assertEquals('demoscope1', $data->getScope());
  }

  public function testFind()
  {
    $result = $this->app['orm']->find('Pantarei\OAuth2\Entity\Scopes', 1);
    $this->assertEquals('Pantarei\OAuth2\Entity\Scopes', get_class($result));
    $this->assertEquals(1, $result->getId());
    $this->assertTrue($result !== NULL);
    $this->assertEquals('demoscope1', $result->getScope());
  }
}
