<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use AuthBucket\OAuth2\Exception\InvalidClientException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\JsonResponse;
use AuthBucket\OAuth2\Util\RedirectResponse;
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
        // Fetch username from authenticated token.
        $username = $this->checkUsername($securityContext);

        // Set client_id from GET.
        $client_id = $this->checkClientId($request, $modelManagerFactory);

        // Check and set redirect_uri.
        $redirect_uri = $this->checkRedirectUri($request, $modelManagerFactory, $client_id);

        // Check and set state.
        $state = $this->checkState($request, $redirect_uri);

        // Check and set scope.
        $scope = $this->checkScope(
            $request,
            $modelManagerFactory,
            $client_id,
            $username,
            $redirect_uri,
            $state
        );

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
