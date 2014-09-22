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

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Validator\Constraints\RefreshToken;
use AuthBucket\OAuth2\Validator\Constraints\Scope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Refresh token grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(Request $request)
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId();

        // Check refresh_token, then fetch username and scope.
        list($username, $scope) = $this->checkRefreshToken($request, $clientId);

        // Generate access_token, store to backend and set token response.
        $parameters = $this->tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope
            );

        return JsonResponse::create($parameters, 200, array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ));
    }

    /**
     * Check refresh_token supplied, return stored username and scope.
     *
     * @param Request $request  Incoming request object.
     * @param string  $clientId Corresponding client_id that refresh_token should belongs to.
     *
     * @return array A list with stored username and scope, originally grant in authorize endpoint.
     *
     * @throw InvalidRequestException If supplied refresh_token or scope in invalid format.
     * @throw InvalidGrantException If refresh_token not belongs to give client_id, or already expired.
     * @throw InvalidScopeException If supplied scope outside supported scope range.
     */
    private function checkRefreshToken(
        Request $request,
        $clientId
    ) {
        // refresh_token must exists and in valid format.
        $refreshToken = $request->request->get('refresh_token');
        $errors = $this->validator->validateValue($refreshToken, array(
            new NotBlank(),
            new RefreshToken(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // scope may not exists, else must be in valid format.
        $scope = $request->request->get('scope');
        $errors = $this->validator->validateValue($scope, array(
            new Scope(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Check refresh_token with database record.
        $refreshTokenManager = $this->modelManagerFactory->getModelManager('refresh_token');
        $result = $refreshTokenManager->readModelOneBy(array(
            'refreshToken' => $refreshToken,
        ));
        if ($result === null || $result->getClientId() !== $clientId) {
            throw new InvalidGrantException(array(
                'error_description' => 'The provided refresh token was issued to another client.',
            ));
        } elseif ($result->getExpires() < new \DateTime()) {
            throw new InvalidGrantException(array(
                'error_description' => 'The provided refresh token is expired.',
            ));
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
            // Compare if given scope within all available granted scopes.
            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $scopeGranted) !== $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope exceeds the scope granted by the resource owner.',
                ));
            }
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($scopeGranted !== null) {
            $scope = $scopeGranted;
        }

        if ($scope !== null) {
            // Compare if given scope within all supported scopes.
            $scopeSupported = array();
            $scopeManager = $this->modelManagerFactory->getModelManager('scope');
            $result = $scopeManager->readModelAll();
            if ($result !== null) {
                foreach ($result as $row) {
                    $scopeSupported[] = $row->getScope();
                }
            }
            if (array_intersect($scope, $scopeSupported) !== $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope is unknown.',
                ));
            }

            // Compare if given scope within all authorized scopes.
            $scopeAuthorized = array();
            $authorizeManager = $this->modelManagerFactory->getModelManager('authorize');
            $result = $authorizeManager->readModelOneBy(array(
                'clientId' => $clientId,
                'username' => $username,
            ));
            if ($result !== null) {
                $scopeAuthorized = $result->getScope();
            }
            if (array_intersect($scope, $scopeAuthorized) !== $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope exceeds the scope granted by the resource owner.',
                ));
            }
        }

        return array($username, $scope);
    }
}
