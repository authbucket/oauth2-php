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
 * Test token type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenTypeTest extends OAuth2WebTestCase
{
  public function testTokenType()
  {
    $request = new Request();
    $stub = $this->getMockForAbstractClass('Pantarei\OAuth2\Extension\TokenType');
  }
}
