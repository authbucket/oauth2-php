<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 grant type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeHandlerInterface
{
    /**
     * Handle corresponding grant type logic.
     *
     * @param Request $request Incoming request object.
     *
     * @return JsonResponse The json response object for token endpoint.
     */
    public function handle(Request $request);
}
