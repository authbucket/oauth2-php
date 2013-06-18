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
 * Shared grant type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    /**
     * Fetch client_id from HTTP Basic Auth or POST.
     *
     * Note that since token endpoint (should) already protected by firewall,
     * where client_id and client_secret already confirm as exist and valid,
     * here we just need to fetch it out.
     *
     * @param Request $request
     *   Incoming request object.
     *
     * @return string
     *   Supplied client_id from incoming request.
     */
    protected function checkClientId(
        Request $request
    )
    {
        return $request->headers->get('PHP_AUTH_USER', null)
            ? $request->headers->get('PHP_AUTH_USER', null)
            : $request->request->get('client_id', null);
    }

    /**
     * Fetch scope from POST.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     *
     * @return array|null
     *   Supplied scope in array from incoming request, or null if none given.
     *
     * @throw InvalidRequestException
     *   If supplied scope in bad format.
     * @throw InvalidScopeException
     *   If supplied scope outside supported scope range.
     */
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

    /**
     * Convert given paramenters into JSON as token endpoint response.
     *
     * @param array $parameters
     *   Parameters going to be response in JSON format.
     *
     * @return JsonResponse
     *   JsonResponse object as token endpoint response.
     */
    protected function setResponse(array $parameters)
    {
        $headers = array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );

        return JsonResponse::create(array_filter($parameters), 200, $headers);
    }
}
