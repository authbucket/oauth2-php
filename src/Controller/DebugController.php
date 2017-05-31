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
use AuthBucket\OAuth2\Symfony\Component\Security\Core\Authentication\Token\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Debug Endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugController
{
    protected $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    public function indexAction(Request $request)
    {
        // Fetch authenticated access token from security context.
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token instanceof AccessToken) {
            throw new ServerErrorException([
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ]);
        }

        // Handle debug endpoint response.
        $parameters = [
            'access_token' => $token->getAccessToken(),
            'token_type' => $token->getTokenType(),
            'client_id' => $token->getClientId(),
            'username' => $token->getUsername(),
            'expires' => $token->getExpires()->getTimestamp(),
            'scope' => $token->getScope(),
        ];

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }
}
