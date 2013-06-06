<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\TokenType;

use Pantarei\OAuth2\TokenType\MacTokenType;
use Pantarei\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test MAC token type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenTypeTest extends WebTestCase
{
    public function testTokenType()
    {
        $request = new Request();
        $token_type = MacTokenType::create($request, $this->app);
        $this->assertNull($token_type->getResponse($request, $this->app));
    }
}
