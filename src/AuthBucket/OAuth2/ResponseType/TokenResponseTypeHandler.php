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

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
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
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $authorizeScopeUri = null
    )
    {
        // Fetch username from authenticated token.
        $username = $this->checkUsername($securityContext);

        // Fetch and check client_id.
        $clientId = $this->checkClientId($request, $modelManagerFactory);

        // Fetch and check redirect_uri.
        $redirectUri = $this->checkRedirectUri($request, $modelManagerFactory, $clientId);

        // Fetch and check state.
        $state = $this->checkState($request, $redirectUri);

        // Fetch and check scope.
        $scope = $this->checkScope(
            $request,
            $modelManagerFactory,
            $clientId,
            $username,
            $redirectUri,
            $state,
            $authorizeScopeUri
        );

        // Generate parameters, store to backend and set response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $clientId,
            $username,
            $scope,
            $state,
            $withRefreshToken = false
        );

        return RedirectResponse::create($redirectUri, $parameters);
    }
}
