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
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
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
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $providerKey
    )
    {
        // Check and set client_id.
        $client_id = $this->checkClientId($request, $modelManagerFactory);

        // Check resource owner credentials
        $username = $this->checkUsername(
            $request,
            $modelManagerFactory,
            $authenticationManager,
            $providerKey
        );

        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagerFactory);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return $this->setResponse($parameters);
    }

    private function checkUsername(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        AuthenticationManagerInterface $authenticationManager,
        $providerKey
    )
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // username and password must exist and in valid format.
        $query = array(
            'username' => $username,
            'password' => $password,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        // Validate credentials with authentication manager.
        $token = new UsernamePasswordToken($username, $password, $providerKey);
        if (null === $authenticationManager->authenticate($token)) {
            throw new InvalidGrantException();
        }

        return $username;
    }
}
