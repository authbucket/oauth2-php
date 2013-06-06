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

use Pantarei\OAuth2\TokenType\BearerTokenType;
use Pantarei\OAuth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test Bearer token type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenTypeTest extends WebTestCase
{
    public function testTokenType()
    {
        // See https://github.com/fabpot/Silex/issues/473#issuecomment-10103129
        $this->app->boot();

        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
            'client_id' => 'http://democlient2.com/',
        );
        $server = array();
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $token_type = BearerTokenType::create($request, $this->app);
        $this->assertTrue($token_type->getResponse($request, $this->app) instanceof Response);
    }
}
