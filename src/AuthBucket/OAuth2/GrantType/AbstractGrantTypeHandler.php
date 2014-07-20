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

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientToken;
use AuthBucket\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Shared grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    /**
     * Fetch client_id from authenticated token.
     *
     * @param SecurityContextInterface $securityContext Incoming request object.
     *
     * @return string Supplied client_id from authenticated token.
     *
     * @throw ServerErrorException If supplied token is not a ClientToken instance.
     */
    protected function checkClientId(
        SecurityContextInterface $securityContext
    )
    {
        $clientId = $securityContext->getToken()->getClientId();

        return $clientId;
    }

    /**
     * Fetch scope from POST.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     *
     * @return array|null Supplied scope in array from incoming request, or null if none given.
     *
     * @throw InvalidRequestException If supplied scope in bad format.
     * @throw InvalidScopeException If supplied scope outside supported scope range.
     */
    protected function checkScope(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId,
        $username
    )
    {
        $scope = $request->request->get('scope', null);

        // scope may not exists.
        if ($scope) {
            // scope must be in valid format.
            if (!Filter::filter(array('scope' => $scope))) {
                throw new InvalidRequestException(array(
                    'error_description' => 'The request includes an invalid parameter value.',
                ));
            }

            // Compare if given scope within all available authorized scopes.
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

            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $scopeAuthorized, $scopeSupported) != $scope) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope exceeds the scope granted by the resource owner.',
                ));
            }
        }

        return $scope;
    }
}
