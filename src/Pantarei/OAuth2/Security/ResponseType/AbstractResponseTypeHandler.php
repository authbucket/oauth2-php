<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\ResponseType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Token response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractResponseTypeHandler implements ResponseTypeHandlerInterface
{
    protected function checkRedirectUri(Request $request, array $modelManagers, $client_id)
    {
        $redirect_uri = $request->query->get('redirect_uri');

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $result = $modelManagers['client']->findClientByClientId($client_id);
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

    protected function checkScope(Request $request, array $modelManagers)
    {
        $scope = $request->query->get('scope', array());

        if ($scope) {
            $stored = array();
            $result = $modelManagers['scope']->findScopes();
            foreach ($result as $row) {
                $stored[] = $row->getScope();
            }

            $scope = preg_split('/\s+/', $request->query->get('scope'));
            if (array_intersect($scope, $stored) !== $scope) {
                throw new InvalidScopeException();
            }
        }

        return $scope;
    }

    protected function checkState(Request $request)
    {
        $state = $request->query->get('state', null);

        if ($state) {
            $query = array('state' => $state);
            $filtered_query = ParameterUtils::filter($query);
            if ($filtered_query != $query) {
                throw new InvalidRequestException();
            }
        }

        return $state;
    }

    protected function setResponse(GetResponseEvent $event, $parameters)
    {
        $redirect_uri = Request::create($redirect_uri, 'GET', array_filter($parameters))->getUri();
        $response = new RedirectResponse($redirect_uri);
        $event->setResponse($response);
    }
}
