<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\GrantType;

use Pantarei\Oauth2\Exception\InvalidClientException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Client credentials grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientCredentialsGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        try {
            // Fetch client_id from authenticated token.
            $client_id = $this->checkClientId($securityContext);

            // No (and not possible to have) username, set as empty string.
            $username = '';

            // Check and set scope.
            $scope = $this->checkScope($request, $modelManagerFactory, $client_id, $username);
        } catch (InvalidClientException $e) {
            return JsonResponse::create(array(
                'error' => 'invalid_client',
            ), 401);
        } catch (InvalidRequestException $e) {
            return JsonResponse::create(array(
                'error' => 'invalid_request',
            ), 400);
        } catch (InvalidScopeException $e) {
            return JsonResponse::create(array(
                'error' => 'invalid_scope',
            ), 400);
        }

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return JsonResponse::create($parameters);
    }
}
