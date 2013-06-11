<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\GrantType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Refresh token grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeHandler implements GrantTypeHandlerInterface
{
    public function handle(
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        $tokenTypeHandler,
        array $modelManagers,
        $client_id,
        $providerKey
    )
    {
        $request = $event->getRequest();

        $query = array(
            'refresh_token' => $request->request->get('refresh_token'),
            'scope' => $request->request->get('scope'),
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        $refresh_token = $request->request->get('refresh_token');

        // Check refresh_token with database record.
        $result = $modelManagers['refresh_token']->findRefreshTokenByRefreshToken($refresh_token);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidRequestException();
        }

        // Fetch scope from pre-grnerated refresh_token.
        $stored = null;
        if ($result !== null && $result->getClientId() == $client_id && $result->getScope()) {
            $stored = $result->getScope();
        }

        // Compare if given scope is subset of original refresh_token's scope.
        $scope = null;
        if ($request->request->get('scope') && $stored !== null) {
            $scope = preg_split('/\s+/', $request->request->get('scope'));
            if (array_intersect($scope, $stored) != $scope) {
                throw new InvalidScopeException();
            }
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($stored !== null) {
            $scope = $stored;
        }

        $username = $result->getUsername();

        // Generate access_token, store to backend and set token response.
        $tokenTypeHandler->setResponse(
            $event,
            $modelManagers,
            $client_id,
            $username,
            $scope
        );
    }
}
