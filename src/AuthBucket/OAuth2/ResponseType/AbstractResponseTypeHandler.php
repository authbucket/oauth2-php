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
     * @param SecurityContextInterface $securityContext
     *                                                  Incoming request object.
     *
     * @return string
     *                Supplied username from authenticated token.
     *
     * @throw ServerErrorException
     *   If supplied token is not a standard TokenInterface instance.
     */
    protected function checkUsername(
        SecurityContextInterface $securityContext
    )
    {
        $token = $securityContext->getToken();
        if (!$token instanceof TokenInterface) {
            throw new ServerErrorException();
        }

        return $token->getUsername();
    }

    /**
     * Fetch cliend_id from GET.
     *
     * @param Request                      $request
     *                                                          Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *                                                          Model manager factory for compare with database record.
     *
     * @return string
     *                Supplied client_id from incoming request.
     *
     * @throw InvalidRequestException
     *   If supplied client_id in bad format.
     * @throw InvalidClientException
     *   If client_id not found from database record.
     */
    protected function checkClientId(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $client_id = $request->query->get('client_id');

        // client_id is required and in valid format.
        $query = array(
            'client_id' => $client_id,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Compare client_id with database record.
        $clientManager = $modelManagerFactory->getModelManager('client');
        $result = $clientManager->findClientByClientId($client_id);
        if ($result === null) {
            throw new InvalidClientException();
        }

        return $client_id;
    }

    /**
     * Fetch redirect_uri from GET.
     *
     * @param Request                      $request
     *                                                          Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *                                                          Model manager factory for compare with database record.
     *                                                          @param string client_id
     *                                                          Corresponding client_id that code should belongs to.
     *
     * @return string
     *                The supplied redirect_uri from incoming request, or from stored
     *                record.
     *
     * @throw InvalidRequestException
     *   If redirect_uri not exists in both incoming request and database
     *   record, or supplied value not match with stord record.
     */
    protected function checkRedirectUri(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id
    )
    {
        $clientManager = $modelManagerFactory->getModelManager('client');

        $redirect_uri = $request->query->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $result = $clientManager->findClientByClientId($client_id);
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirect_uri) {
            throw new InvalidRequestException();
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirect_uri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirect_uri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException();
            }
        }

        return $redirect_uri ? $redirect_uri : $stored;
    }

    protected function checkState(
        Request $request,
        $redirect_uri
    )
    {
        $state = $request->query->get('state', null);

        // state may not exists.
        if ($state) {
            // state must be in valid format.
            $query = array(
                'state' => $state,
            );
            if (!Filter::filter($query)) {
                throw new InvalidRequestException(array(
                    'redirect_uri' => $redirect_uri
                ));
            }
        }

        return $state;
    }

    protected function checkScope(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username,
        $redirect_uri,
        $state
    )
    {
        $scope = $request->query->get('scope', array());

        // scope may not exists.
        if ($scope) {
            // scope must be in valid format.
            $query = array(
                'scope' => $scope,
            );
            if (!Filter::filter($query)) {
                throw new InvalidRequestException(array(
                    'redirect_uri' => $redirect_uri,
                    'state' => $state,
                ));
            }

            // Compare if given scope within all available authorized scopes.
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

            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $authorized_scope, $supported_scope) != $scope) {
                throw new InvalidScopeException(array(
                    'redirect_uri' => $redirect_uri,
                    'state' => $state,
                ));
            }
        }

        return $scope;
    }
}
