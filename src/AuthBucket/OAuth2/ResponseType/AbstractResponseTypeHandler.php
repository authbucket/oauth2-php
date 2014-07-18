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
use AuthBucket\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Shared response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractResponseTypeHandler implements ResponseTypeHandlerInterface
{
    /**
     * Fetch username from authenticated token.
     *
     * @param SecurityContextInterface $securityContext Incoming request object.
     *
     * @return string Supplied username from authenticated token.
     *
     * @throw ServerErrorException If supplied token is not a standard TokenInterface instance.
     */
    protected function checkUsername(
        SecurityContextInterface $securityContext
    )
    {
        $username = $securityContext->getToken()->getUsername();

        return $username;
    }

    /**
     * Fetch cliend_id from GET.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     *
     * @return string Supplied client_id from incoming request.
     *
     * @throw InvalidRequestException If supplied client_id in bad format.
     * @throw InvalidClientException If client_id not found from database record.
     */
    protected function checkClientId(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $clientId = $request->query->get('client_id');

        // client_id is required and in valid format.
        if (!Filter::filter(array('client_id' => $clientId))) {
            throw new InvalidRequestException();
        }

        // Compare client_id with database record.
        $clientManager = $modelManagerFactory->getModelManager('client');
        $result = $clientManager->findClientByClientId($clientId);
        if ($result === null) {
            throw new InvalidClientException();
        }

        return $clientId;
    }

    /**
     * Fetch redirect_uri from GET.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     * @param string                       $clientId            Corresponding client_id that code should belongs to.
     *
     * @return string The supplied redirect_uri from incoming request, or from stored record.
     *
     * @throw InvalidRequestException If redirect_uri not exists in both incoming request and database record, or supplied value not match with stord record.
     */
    protected function checkRedirectUri(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId
    )
    {
        $clientManager = $modelManagerFactory->getModelManager('client');

        $redirectUri = $request->query->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $result = $clientManager->findClientByClientId($clientId);
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirectUri) {
            throw new InvalidRequestException();
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirectUri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirectUri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException();
            }
        }

        return $redirectUri
            ?: $stored;
    }

    protected function checkState(
        Request $request,
        $redirectUri
    )
    {
        $state = $request->query->get('state');

        // state is required and in valid format.
        if (!Filter::filter(array('state' => $state))) {
            throw new InvalidRequestException(array(
                'redirect_uri' => $redirectUri
            ));
        }

        return $state;
    }

    protected function checkScope(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId,
        $username,
        $redirectUri,
        $state
    )
    {
        $scope = $request->query->get('scope', array());

        // scope may not exists.
        if ($scope) {
            // scope must be in valid format.
            if (!Filter::filter(array('scope' => $scope))) {
                throw new InvalidRequestException(array(
                    'redirect_uri' => $redirectUri,
                    'state' => $state,
                ));
            }

            $scope = preg_split('/\s+/', $scope);

            // Compare if given scope within all supported scopes.
            $scopeSupported = array();
            $scopeManager = $modelManagerFactory->getModelManager('scope');
            $result = $scopeManager->findScopes();
            if ($result !== null) {
                foreach ($result as $row) {
                    $scopeSupported[] = $row->getScope();
                }
            }
            if (array_intersect($scope, $scopeSupported) !== $scope) {
                throw new InvalidScopeException(array(
                    'redirect_uri' => $redirectUri,
                    'state' => $state,
                    'error_description' => 'The requested scope is unknown.',
                ));
            }

            // Compare if given scope within all authorized scopes.
            $scopeAuthorized = array();
            $authorizeManager = $modelManagerFactory->getModelManager('authorize');
            $result = $authorizeManager->findAuthorizeByClientIdAndUsername($clientId, $username);
            if ($result !== null) {
                $scopeAuthorized = $result->getScope();
            }
            if (array_intersect($scope, $scopeAuthorized) !== $scope) {
                throw new InvalidScopeException(array(
                    'redirect_uri' => $redirectUri,
                    'state' => $state,
                    'error_description' => 'The requested scope is invalid.',
                ));
            }
        }

        return $scope;
    }
}
