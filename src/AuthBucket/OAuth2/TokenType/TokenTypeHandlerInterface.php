<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\TokenType;

use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 token type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface TokenTypeHandlerInterface
{
    /**
     * Fetch access_token from given request.
     *
     * @param Request $request Incoming request object.
     *
     * @return string Fetched access_token from incoming request.
     */
    public function getAccessToken(Request $request);

    /**
     * Create and save access_token parameters for generate response.
     *
     * @param string $clientId         client_id this access token should belongs to.
     * @param string $username         username this access token should belongs to.
     * @param array  $scope            All scope that this access token grant.
     * @param string $state            Original state which should preserve.
     * @param bool   $withRefreshToken False for response_type=token.
     *
     * @return array All parameters for generate response.
     */
    public function createAccessToken(
        $clientId,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    );
}
