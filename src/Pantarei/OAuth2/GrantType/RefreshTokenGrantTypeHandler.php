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

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Refresh token grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        // Check and set client_id.
        $client_id = $this->checkClientId($request);

        // Check refresh_token, then fetch username and scope.
        list($username, $scope) = $this->checkRefreshToken($request, $modelManagerFactory, $client_id);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return $this->setResponse($parameters);
    }

    /**
     * Check refresh_token supplied, return stored username and scope.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     * @param string client_id
     *   Corresponding client_id that refresh_token should belongs to.
     *
     * @return array
     *   A list with stored username and scope, originally grant in authorize
     *   endpoint.
     *
     * @throw InvalidRequestException
     *   If supplied refresh_token or scope in invalid format.
     * @throw InvalidGrantException
     *   If refresh_token not belongs to give client_id, or already expired.
     * @throw InvalidScopeException
     *   If supplied scope outside supported scope range.
     */
    private function checkRefreshToken(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $refresh_token = $request->request->get('refresh_token');
        $scope = $request->request->get('scope', null);

        $refreshTokenManager = $modelManagerFactory->getModelManager('refresh_token');

        // refresh_token must exists and in valid format.
        $query = array(
            'refresh_token' => $refresh_token,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Check refresh_token with database record.
        $result = $refreshTokenManager->findRefreshTokenByRefreshToken($refresh_token);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidGrantException();
        }

        // Fetch username from stored refresh_token.
        $username = $result->getUsername();

        // Fetch scope from pre-grnerated refresh_token.
        $stored = null;
        if ($result !== null && $result->getClientId() == $client_id && $result->getScope()) {
            $stored = $result->getScope();
        }

        // Compare if given scope is subset of original refresh_token's scope.
        if ($scope !== null && $stored !== null) {
            // scope must be in valid format.
            $query = array(
                'scope' => $scope,
            );
            if (!Filter::filter($query)) {
                throw new InvalidRequestException();
            }

            // Compare if given scope within all available stored scopes.
            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $stored) != $scope) {
                throw new InvalidScopeException();
            }
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($stored !== null) {
            $scope = $stored;
        }

        return array($username, $scope);
    }
}
