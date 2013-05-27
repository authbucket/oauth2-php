<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base OAuth2 type interface for response, grant and token type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface TypeInterface
{
    public static function create(Request $request, Application $app);

    public function getResponse(Request $request, Application $app);
}
