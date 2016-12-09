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

use AuthBucket\OAuth2\Validator\Constraints\Password;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Password grant type implementation.
 *
 * Note this is a Symfony based specific implementation, 3rd party
 * integration should override this with its own logic.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(Request $request)
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId($request);

        // Check resource owner credentials
        $username = $this->checkUsername($request);

        // Check and set scope.
        $scope = $this->checkScope($request, $clientId, $username);

        // Generate access_token, store to backend and set token response.
        $parameters = $this->tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope
            );

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }
}
