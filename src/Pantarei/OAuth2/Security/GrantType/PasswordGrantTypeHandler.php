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
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Password grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeHandler implements GrantTypeHandlerInterface
{
    public function handle(
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        $tokenTypeHandler,
        array $modelManagers,
        $client_id,
        $providerKey
    )
    {
        $request = $event->getRequest();

        $query = array(
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'scope' => $request->request->get('scope'),
        );
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $token = new UsernamePasswordToken($username, $password, $providerKey);
        if (null === $authenticationManager->authenticate($token)) {
            throw new InvalidRequestException();
        }

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

        // Generate access_token, store to backend and set token response.
        $tokenTypeHandler->setResponse(
            $event,
            $modelManagers,
            $client_id,
            $username,
            $scope
        );
    }
}
