<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessTokenToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 debug endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugController
{
    protected $securityContext;

    public function __construct(
        SecurityContextInterface $securityContext
    )
    {
        $this->securityContext = $securityContext;
    }

    public function debugAction(Request $request)
    {
        // Fetch authenticated access token from security context.
        $token = $this->securityContext->getToken();
        if ($token === null || !$token instanceof AccessTokenToken) {
            throw new ServerErrorException(array(
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ));
        }

        // Handle debug endpoint response.
        $parameters = array(
            'access_token' => $token->getAccessToken(),
            'token_type' => $token->getTokenType(),
            'client_id' => $token->getClientId(),
            'username' => $token->getUsername(),
            'expires' => $token->getExpires()->getTimestamp(),
            'scope' => $token->getScope(),
        );

        return JsonResponse::create($parameters, 200, array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ));
    }
}
