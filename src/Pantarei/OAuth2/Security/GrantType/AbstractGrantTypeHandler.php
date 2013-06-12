<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\GrantType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Abstract grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractGrantTypeHandler implements GrantTypeHandlerInterface
{
    protected function checkClientId(Request $request)
    {
        return $request->headers->get('PHP_AUTH_USER', false)
            ? $request->headers->get('PHP_AUTH_USER', false)
            : $request->request->get('client_id', false);
    }

    protected function checkScope(Request $request, $modelManagers)
    {
        // Compare if given scope within all available stored scopes.
        $stored = array();
        $result = $modelManagers['scope']->findScopes();
        foreach ($result as $row) {
            $stored[] = $row->getScope();
        }

        $scope = preg_split('/\s+/', $request->request->get('scope'));
        if (array_intersect($scope, $stored) !== $scope) {
            throw new InvalidScopeException();
        }

        return $scope;
    }

    protected function setResponse(GetResponseEvent $event, $parameters)
    {
        $headers = array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );
        $response = JsonResponse::create(array_filter($parameters), 200, $headers);
        $event->setResponse($response);
    }
}
