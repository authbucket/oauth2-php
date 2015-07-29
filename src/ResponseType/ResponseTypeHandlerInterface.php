<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 response type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResponseTypeHandlerInterface
{
    /**
     * Handle corresponding response type logic.
     *
     * @param Request $request Incoming request object.
     *
     * @return RedirectResponse The redirect response object for authorize endpoint.
     */
    public function handle(Request $request);
}
