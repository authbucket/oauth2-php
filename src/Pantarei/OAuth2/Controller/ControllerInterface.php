<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * A simple controller interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ControllerInterface
{
    /**
     * Handle page callback and return the response object.
     *
     * @param Request $request
     *   The incoming request object.
     *
     * @return Response
     *   The corresponding response object, handle by different handler.
     */
    public function handle(Request $request);
}
