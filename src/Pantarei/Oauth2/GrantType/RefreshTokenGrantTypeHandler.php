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
use Pantarei\Oauth2\Exception\InvalidGrantException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\Oauth2\Util\Filter;
use Pantarei\Oauth2\Util\JsonResponse;
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
        try {
            // Fetch client_id from authenticated token.
            $client_id = $this->checkClientId($securityContext);

            // Check refresh_token, then fetch username and scope.
            list($username, $scope) = $this->checkRefreshToken($request, $modelManagerFactory, $client_id);
        } catch (InvalidClientException $e) {
            return JsonResponse::create(array(
                'error' => 'invalid_client',
            ), 401);
        } catch (InvalidGrantException $e) {
            return JsonResponse::create(array(
                'error' => 'invalid_grant',
            ), 400);
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

        // refresh_token must exists and in valid format.
        $query = array(
            'refresh_token' => $refresh_token,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Check refresh_token with database record.
        $refreshTokenManager = $modelManagerFactory->getModelManager('refresh_token');
        $result = $refreshTokenManager->findRefreshTokenByRefreshToken($refresh_token);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < new \DateTime()) {
            throw new InvalidGrantException();
        }

        // Fetch username from stored refresh_token.
        $username = $result->getUsername();

        // Fetch scope from pre-grnerated refresh_token.
        $granted_scope = null;
        if ($result !== null && $result->getClientId() == $client_id && $result->getScope()) {
            $granted_scope = $result->getScope();
        }

        // Compare if given scope is subset of original refresh_token's scope.
        if ($scope !== null && $granted_scope !== null) {
            // scope must be in valid format.
            $query = array(
                'scope' => $scope,
            );
            if (!Filter::filter($query)) {
                throw new InvalidRequestException();
            }

            // Compare if given scope within all available granted scopes.
            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $granted_scope) != $scope) {
                throw new InvalidScopeException();
            }
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($granted_scope !== null) {
            $scope = $granted_scope;
        }

        if ($scope !== null) {
            // Compare if given scope within all available stored scopes.
            $authorized_scope = array();
            $authorizeManager = $modelManagerFactory->getModelManager('authorize');
            $result = $authorizeManager->findAuthorizeByClientIdAndUsername($client_id, $username);
            if ($result !== null) {
                $authorized_scope = $result->getScope();
            }

            $supported_scope = array();
            $scopeManager = $modelManagerFactory->getModelManager('scope');
            $result = $scopeManager->findScopes();
            if ($result !== null) {
                foreach ($result as $row) {
                    $supported_scope[] = $row->getScope();
                }
            }

            if (array_intersect($scope, $authorized_scope, $supported_scope) != $scope) {
                throw new InvalidScopeException();
            }
        }

        return array($username, $scope);
    }
}
