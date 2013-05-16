<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Extension;

use Pantarei\OAuth2\Extension\GrantType;
use Pantarei\OAuth2\OAuth2WebTestCase;

/**
 * Test grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class GrantTypeTest extends OAuth2WebTestCase
{
  public function testGrantType()
  {
    $response_type = new GrantType($this->app);
    $this->assertTrue($response_type->buildType(array(), array()));
    $this->assertTrue($response_type->buildView());
    $this->assertTrue($response_type->finishView());
    $this->assertEquals('grant_type', $response_type->getName());
    $this->assertNull($response_type->getParent());
  }
}
