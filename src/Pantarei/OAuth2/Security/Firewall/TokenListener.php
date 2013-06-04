<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\Firewall;

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Security\Authentication\Token\ClientToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * TokenEndpointListener implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenListener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // At least one (and only one) of client credentials method required.
        if (!$request->headers->get('PHP_AUTH_USER', false) && !$request->request->get('client_id', false)) {
            throw new InvalidRequestException();
        } elseif ($request->headers->get('PHP_AUTH_USER', false) && $request->request->get('client_id', false)) {
            throw new InvalidRequestException();
        }

        // Check with HTTP basic auth if exists.
        if ($request->headers->get('PHP_AUTH_USER', false)) {
            $client_id = $request->headers->get('PHP_AUTH_USER', false);
            $client_secret = $request->headers->get('PHP_AUTH_PW', false);
        } else {
            $client_id = $request->request->get('client_id', false);
            $client_secret = $request->request->get('client_secret', false);
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof ClientToken
                && $token->isAuthenticated()
                && $token->getUsername() === $client_id) {
                    return;
                }
        }

        try {
            $token = $this->authenticationManager
                ->authenticate(new ClientToken($client_id, $client_secret));
            $this->securityContext->setToken($token);
        } catch (AuthenticationException $failed) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }
}
