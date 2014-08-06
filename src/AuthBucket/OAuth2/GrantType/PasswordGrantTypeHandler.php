<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use AuthBucket\OAuth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
    public function handle(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId($securityContext);

        // Check resource owner credentials
        $username = $this->checkUsername(
            $request,
            $modelManagerFactory,
            $userProvider,
            $userChecker,
            $encoderFactory
        );

        // Check and set scope.
        $scope = $this->checkScope($request, $modelManagerFactory, $clientId, $username);

        // Generate access_token, store to backend and set token response.
        $parameters = $tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope
            );

        return JsonResponse::create($parameters);
    }

    /**
     * Fetch username from POST.
     *
     * @param Request                      $request             Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory Model manager factory for compare with database record.
     *
     * @return string The supplied username.
     *
     * @throw InvalidRequestException If username or password in invalid format.
     * @throw InvalidGrantException If reported as bad credentials from authentication provider.
     */
    private function checkUsername(
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        UserProviderInterface $userProvider,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // username and password must exist and in valid format.
        if (!Filter::filter(array(
            'username' => $username,
            'password' => $password,
        ))) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Validate credentials with authentication manager.
        try {
            $token = new UsernamePasswordToken($username, $password, 'oauth2');
            $authenticationProvider = new DaoAuthenticationProvider(
                $userProvider,
                $userChecker,
                'oauth2',
                $encoderFactory
            );
            $authenticationProvider->authenticate($token);
        } catch (BadCredentialsException $e) {
            throw new InvalidGrantException(array(
                'error_description' => 'Client authentication failed.',
            ));
        }

        return $username;
    }
}
