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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerInterface;
use Pantarei\OAuth2\Util\Filter;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    protected function checkClientId(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $client_id = $request->headers->get('PHP_AUTH_USER', null)
            ? $request->headers->get('PHP_AUTH_USER', null)
            : $request->request->get('client_id', null);

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

    protected function checkScope(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $scope = $request->request->get('scope', null);
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

    protected function setResponse($parameters)
    {
        $headers = array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );

        return JsonResponse::create(array_filter($parameters), 200, $headers);
    }
}
