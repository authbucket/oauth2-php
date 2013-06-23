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

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Password grant type implementation.
 *
 * Note this is a Symfony based specific implementation, 3rd party
 * integration should override this with its own logic.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeHandler extends AbstractGrantTypeHandler
{
    protected $authenticationProvider;

    public function __construct(
        AuthenticationProviderInterface $authenticationProvider
    )
    {
        $this->authenticationProvider = $authenticationProvider;
    }

    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        // Fetch client_id from authenticated token.
        $client_id = $this->checkClientId($securityContext);

        // Check resource owner credentials
        $username = $this->checkUsername($request, $modelManagerFactory);

        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagerFactory, $client_id, $username);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory->getTokenTypeHandler()->createAccessToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope
        );
        return $this->setResponse($parameters);
    }

    /**
     * Fetch username from POST.
     *
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     *
     * @return string
     *   The supplied username.
     *
     * @throw InvalidRequestException
     *   If username or password in invalid format.
     * @throw InvalidGrantException
     *   If reported as bad credentials from authentication provider.
     */
    private function checkUsername(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory
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
        try {
            $token = new UsernamePasswordToken($username, $password, 'oauth2');
            $this->authenticationProvider->authenticate($token);
        } catch (BadCredentialsException $e) {
            throw new InvalidGrantException();
        }

        return $username;
    }
}
