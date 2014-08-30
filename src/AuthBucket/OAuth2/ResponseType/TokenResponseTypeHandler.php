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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Token response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseTypeHandler extends AbstractResponseTypeHandler
{
    public function handle(Request $request)
    {
        // Fetch username from authenticated token.
        $username = $this->checkUsername();

        // Fetch and check client_id.
        $clientId = $this->checkClientId($request);

        // Fetch and check redirect_uri.
        $redirectUri = $this->checkRedirectUri($request, $clientId);

        // Fetch and check state.
        $state = $this->checkState($request, $redirectUri);

        // Fetch and check scope.
        $scope = $this->checkScope(
            $request,
            $clientId,
            $username,
            $redirectUri,
            $state
        );

        // Generate parameters, store to backend and set response.
        $parameters = $this->tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope,
                $state,
                $withRefreshToken = false
            );

        $redirectUri = Request::create($redirectUri, 'GET', $parameters)->getUri();

        return RedirectResponse::create($redirectUri);
    }
}
