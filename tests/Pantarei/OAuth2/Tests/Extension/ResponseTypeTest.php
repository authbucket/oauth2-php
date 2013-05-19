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

use Pantarei\OAuth2\OAuth2WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test response type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseTypeTest extends OAuth2WebTestCase
{
  public function testResponseType()
  {
    $request = new Request();
    $stub = $this->getMockForAbstractClass('Pantarei\OAuth2\Extension\ResponseType', array($request, $this->app));
    $this->assertTrue($stub->__construct($request, $this->app));
    $this->assertTrue($stub->getResponse() instanceof Response);
    $this->assertNull($stub->getParent());
    $this->assertEquals('response_type', $stub->getName());
  }
}
