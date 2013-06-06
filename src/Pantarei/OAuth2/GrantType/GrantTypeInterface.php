<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the interface for grant type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeInterface
{
    public static function create(Request $request, Application $app);

    public function getResponse(Request $request, Application $app);
}
