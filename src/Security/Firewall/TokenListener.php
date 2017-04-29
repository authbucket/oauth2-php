<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Firewall;

use AuthBucket\OAuth2\Exception\InvalidGrantException;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientCredentialsToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * TokenListener implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenListener implements ListenerInterface
{
    protected $providerKey;
    protected $tokenStorage;
    protected $authenticationManager;
    protected $validator;
    protected $logger;
    protected $encoderFactory;
    protected $userProvider;

    public function __construct(
        $providerKey,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        EncoderFactoryInterface $encoderFactory,
        UserProviderInterface $userProvider
    ) {
        $this->providerKey = $providerKey;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->encoderFactory = $encoderFactory;
        $this->userProvider = $userProvider;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // At least one (and only one) of client credentials method required.
        if (!$request->headers->get('PHP_AUTH_USER', false) && !$request->request->get('client_id', false)) {
            throw new InvalidRequestException([
                'error_description' => 'The request is missing a required parameter',
            ]);
        } elseif ($request->headers->get('PHP_AUTH_USER', false) && $request->request->get('client_id', false)) {
            throw new InvalidRequestException([
                'error_description' => 'The request utilizes more than one mechanism for authenticating the client',
            ]);
        }

        // Check with HTTP basic auth if exists.
        if ($request->headers->get('PHP_AUTH_USER', false)) {
            $clientId = $request->headers->get('PHP_AUTH_USER', false);
            $clientSecret = $request->headers->get('PHP_AUTH_PW', false);
        } else {
            $clientId = $request->request->get('client_id', false);
            $clientSecret = $request->request->get('client_secret', false);
        }

        // client_id must in valid format.
        $errors = $this->validator->validate($clientId, [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
            new \AuthBucket\OAuth2\Symfony\Component\Validator\Constraints\ClientId(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // client_secret must in valid format.
        $errors = $this->validator->validate($clientId, [
            new \Symfony\Component\Validator\Constraints\NotBlank(),
            new \AuthBucket\OAuth2\Symfony\Component\Validator\Constraints\ClientSecret(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // grant_type must in valid format.
        $grantType = $request->request->get('grant_type');
        $errors = $this->validator->validate($grantType, [
            new NotBlank(),
            new GrantType(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // check username and password if grant_type = password.
        if ($grantType == 'password') {
            // username must exist and in valid format.
            $username = $request->request->get('username');
            $errors = $this->validator->validate($username, [
                new NotBlank(),
                new Username(),
            ]);
            if (count($errors) > 0) {
                throw new InvalidRequestException([
                    'error_description' => 'The request includes an invalid parameter value.',
                ]);
            }

            // password must exist and in valid format.
            $password = $request->request->get('password');
            $errors = $this->validator->validate($password, [
                new NotBlank(),
                new Password(),
            ]);
            if (count($errors) > 0) {
                throw new InvalidRequestException([
                    'error_description' => 'The request includes an invalid parameter value.',
                ]);
            }

            // Validate credentials with authentication manager.
            try {
                $token = new UsernamePasswordToken($username, $password, 'oauth2');
                $authenticationProvider = new DaoAuthenticationProvider(
                    $this->userProvider,
                    new UserChecker(),
                    'oauth2',
                    $this->encoderFactory
                );
                $authenticationProvider->authenticate($token);
            } catch (BadCredentialsException $e) {
                throw new InvalidGrantException([
                    'error_description' => 'The provided resource owner credentials is invalid.',
                ]);
            }
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Token endpoint client credentials found for client_id "%s"', $clientId));
        }

        if (null !== $token = $this->tokenStorage->getToken()) {
            if ($token instanceof ClientCredentialsToken
                && $token->isAuthenticated()
                && $token->getClientId() === $clientId
            ) {
                return;
            }
        }

        $token = new ClientCredentialsToken(
            $this->providerKey,
            $clientId,
            $clientSecret
        );
        $tokenAuthenticated = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($tokenAuthenticated);
    }
}
