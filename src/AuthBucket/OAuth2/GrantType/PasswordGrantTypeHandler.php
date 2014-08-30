<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    public function handle(Request $request)
    {
        // Fetch client_id from authenticated token.
        $clientId = $this->checkClientId();

        // Check resource owner credentials
        $username = $this->checkUsername($request);

        // Check and set scope.
        $scope = $this->checkScope($request, $clientId, $username);

        // Generate access_token, store to backend and set token response.
        $parameters = $this->tokenTypeHandlerFactory
            ->getTokenTypeHandler()
            ->createAccessToken(
                $clientId,
                $username,
                $scope
            );

        return JsonResponse::create($parameters, 200, array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ));
    }

    /**
     * Fetch username from POST.
     *
     * @param Request $request Incoming request object.
     *
     * @return string The supplied username.
     *
     * @throw InvalidRequestException If username or password in invalid format.
     * @throw InvalidGrantException If reported as bad credentials from authentication provider.
     */
    private function checkUsername(Request $request)
    {
        // username must exist and in valid format.
        $username = $request->request->get('username');
        $errors = $this->validator->validateValue($username, array(
            new NotBlank(),
            new Username(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // password must exist and in valid format.
        $password = $request->request->get('password');
        $errors = $this->validator->validateValue($password, array(
            new NotBlank(),
            new Password(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Validate credentials with authentication manager.
        try {
            $token = new UsernamePasswordToken($username, $password, 'oauth2');
            $authenticationProvider = new DaoAuthenticationProvider(
                $this->userProvider,
                $this->userChecker,
                'oauth2',
                $this->encoderFactory
            );
            $authenticationProvider->authenticate($token);
        } catch (BadCredentialsException $e) {
            throw new InvalidGrantException(array(
                'error_description' => 'The provided resource owner credentials is invalid.',
            ));
        }

        return $username;
    }
}
