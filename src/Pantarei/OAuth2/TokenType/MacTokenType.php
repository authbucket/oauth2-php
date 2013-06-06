<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\TokenType;

use Pantarei\OAuth2\TokenType\TokenTypeInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MAC token type implementation.
 *
 * TODO: This is not yet supported!
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenType implements TokenTypeInterface
{
    public static function create(Request $request, Application $app)
    {
        return new static();
    }

    public function getResponse(Request $request, Application $app)
    {
        return null;
    }
}
