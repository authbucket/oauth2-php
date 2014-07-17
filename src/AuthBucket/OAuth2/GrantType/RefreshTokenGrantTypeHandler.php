<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use AuthBucket\OAuth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Refresh token grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId($securityContext);

        // Check refresh_token, then fetch username and scope.
        list($username, $scope) = $this->checkRefreshToken($request, $modelManagerFactory, $clientId);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $clientId,
            $username,
            $scope
        );

        return JsonResponse::create($parameters);
    }

    /**
     * Check refresh_token supplied, return stored username and scope.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     * @param string                       $clientId            Corresponding client_id that refresh_token should belongs to.
     *
     * @return array A list with stored username and scope, originally grant in authorize endpoint.
     *
     * @throw InvalidRequestException If supplied refresh_token or scope in invalid format.
     * @throw InvalidGrantException If refresh_token not belongs to give client_id, or already expired.
     * @throw InvalidScopeException If supplied scope outside supported scope range.
     */
    private function checkRefreshToken(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId
    )
    {
        $refreshToken = $request->request->get('refresh_token');
        $scope = $request->request->get('scope', null);

        // refresh_token must exists and in valid format.
        if (!Filter::filter(array('refresh_token' => $refreshToken))) {
            throw new InvalidRequestException();
        }

        // Check refresh_token with database record.
        $refreshTokenManager = $modelManagerFactory->getModelManager('refresh_token');
        $result = $refreshTokenManager->findRefreshTokenByRefreshToken($refreshToken);
        if ($result === null || $result->getClientId() !== $clientId) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < new \DateTime()) {
            throw new InvalidGrantException();
        }

        // Fetch username from stored refresh_token.
        $username = $result->getUsername();

        // Fetch scope from pre-grnerated refresh_token.
        $scopeGranted = null;
        if ($result !== null && $result->getClientId() == $clientId && $result->getScope()) {
            $scopeGranted = $result->getScope();
        }

        // Compare if given scope is subset of original refresh_token's scope.
        if ($scope !== null && $scopeGranted !== null) {
            // scope must be in valid format.
            if (!Filter::filter(array('scope' => $scope))) {
                throw new InvalidRequestException();
            }

            // Compare if given scope within all available granted scopes.
            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $scopeGranted) != $scope) {
                throw new InvalidScopeException();
            }
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($scopeGranted !== null) {
            $scope = $scopeGranted;
        }

        if ($scope !== null) {
            // Compare if given scope within all available stored scopes.
            $scopeAuthorized = array();
            $authorizeManager = $modelManagerFactory->getModelManager('authorize');
            $result = $authorizeManager->findAuthorizeByClientIdAndUsername($clientId, $username);
            if ($result !== null) {
                $scopeAuthorized = $result->getScope();
            }

            $scopeSupported = array();
            $scopeManager = $modelManagerFactory->getModelManager('scope');
            $result = $scopeManager->findScopes();
            if ($result !== null) {
                foreach ($result as $row) {
                    $scopeSupported[] = $row->getScope();
                }
            }

            if (array_intersect($scope, $scopeAuthorized, $scopeSupported) != $scope) {
                throw new InvalidScopeException();
            }
        }

        return array($username, $scope);
    }
}
