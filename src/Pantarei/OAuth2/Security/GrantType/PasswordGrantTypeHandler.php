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
 * Password grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeHandler extends AbstractGrantTypeHandler
{
    public function handle(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        TokenTypeHandlerInterface $tokenTypeHandler,
        array $modelManagers,
        $providerKey
    )
    {
        $request = $event->getRequest();

        $client_id = $this->checkClientId($request);

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

        $scope = $this->checkScope($request, $modelManagers);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandler->createToken(
            $modelManagers,
            $client_id,
            $username,
            $scope
        );
        $this->setResponse($event, $parameters);
    }
}
