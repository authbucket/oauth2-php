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

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientToken;
use AuthBucket\OAuth2\Validator\Constraints\ClientId;
use AuthBucket\OAuth2\Validator\Constraints\ClientSecret;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
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

    public function __construct(
        $providerKey,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->providerKey = $providerKey;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->validator = $validator;
        $this->logger = $logger;
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
            new NotBlank(),
            new ClientId(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // client_secret must in valid format.
        $errors = $this->validator->validate($clientId, [
            new NotBlank(),
            new ClientSecret(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Token endpoint client credentials found for client_id "%s"', $clientId));
        }

        if (null !== $token = $this->tokenStorage->getToken()) {
            if ($token instanceof ClientToken
                && $token->isAuthenticated()
                && $token->getClientId() === $clientId
            ) {
                return;
            }
        }

        $token = new ClientToken(
            $this->providerKey,
            $clientId,
            $clientSecret
        );
        $tokenAuthenticated = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($tokenAuthenticated);
    }
}
