<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\ResponseType;

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Token response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractResponseTypeHandler implements ResponseTypeHandlerInterface
{
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

    protected function checkScope(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $scope = $request->query->get('scope', array());
        $scopeManager = $modelManagerFactory->getModelManager('scope');

        // scope may not exists.
        if ($scope) {
            // scope must be in valid format.
            $query = array(
                'scope' => $scope,
            );
            if (!Filter::filter($query)) {
                throw new InvalidRequestException();
            }

            // Compare if given scope within all available stored scopes.
            $stored = array();
            $result = $scopeManager->findScopes();
            foreach ($result as $row) {
                $stored[] = $row->getScope();
            }

            $scope = preg_split('/\s+/', $scope);
            if (array_intersect($scope, $stored) !== $scope) {
                throw new InvalidScopeException();
            }
        }

        return $scope;
    }

    protected function checkState(Request $request)
    {
        $state = $request->query->get('state', null);

        // state may not exists.
        if ($state) {
            // state must be in valid format.
            $query = array(
                'state' => $state,
            );
            if (!Filter::filter($query)) { 
                throw new InvalidRequestException();
            }
        }

        return $state;
    }

    protected function setResponse($redirect_uri, $parameters)
    {
        $redirect_uri = Request::create($redirect_uri, 'GET', array_filter($parameters))->getUri();
        return new RedirectResponse($redirect_uri);
    }
}
