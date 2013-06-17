<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Client credentials grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
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
        // Check and set client_id.
        $client_id = $this->checkClientId($request, $modelManagerFactory);

        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagerFactory);

        // No (and not possible to have) username, set as empty string.
        $username = '';

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return $this->setResponse($parameters);
    }
}
