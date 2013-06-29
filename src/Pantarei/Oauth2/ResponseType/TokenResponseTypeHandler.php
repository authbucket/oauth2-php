<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\ResponseType;

use Pantarei\Oauth2\Exception\InvalidClientException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Exception\ServerErrorException;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Token response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseTypeHandler extends AbstractResponseTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        try {
            // Fetch username from authenticated token.
            $username = $this->checkUsername($securityContext);

            // Set client_id from GET.
            $client_id = $this->checkClientId($request, $modelManagerFactory);

            // Check and set redirect_uri.
            $redirect_uri = $this->checkRedirectUri($request, $modelManagerFactory, $client_id);
        } catch (InvalidClientException $e) {
            return Response::create('invalid_client', 500);
        } catch (InvalidRequestException $e) {
            return Response::create('invalid_request', 500);
        } catch (ServerErrorException $e) {
            return Response::create('server_error', 500);
        }

        try {
            // Check and set scope.
            $scope = $this->checkScope($request, $modelManagerFactory, $client_id, $username);

            // Check and set state.
            $state = $this->checkState($request);
        } catch (InvalidRequestException $e) {
            return RedirectResponse::create($redirect_uri, array('error' => 'invalid_request'));
        } catch (InvalidScopeException $e) {
            return RedirectResponse::create($redirect_uri, array('error' => 'invalid_scope'));
        }

        // Generate parameters, store to backend and set response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope,
            $state,
            $withRefreshToken = false
        );

        return RedirectResponse::create($redirect_uri, $parameters);
    }
}
