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
use Pantarei\OAuth2\Security\Authentication\Token\BearerToken;
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
class BearerTokenListener implements ListenerInterface
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

        $headers_token = $request->headers->get('Authorization', false);
        if ($headers_token && preg_match('/Bearer\s*([^\s]+)/', $headers_token, $matches)) {
            $headers_token = $matches[1];
        } else {
            $headers_token = false;
        }

        $request_token = $request->request->get('access_token', false);
        $query_token = $request->query->get('access_token', false);

        // At least one (and only one) of client credentials method required.
        if (!$headers_token && !$request_token && !$query_token) {
            throw new InvalidRequestException();
        } elseif (($headers_token && $request_token)
            || ($request_token && $query_token)
            || ($query_token && $headers_token)) {
            throw new InvalidRequestException();
        }

        // Check with HTTP basic auth if exists.
        if ($headers_token) {
            $access_token = $headers_token;
        } elseif ($request_token) {
            $access_token = $request_token;
        } elseif ($query_token) {
            $access_token = $query_token;
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof BearerToken
                && $token->isAuthenticated()
                && $token->getCredentials() === $access_token) {
                    return;
                }
        }

        try {
            $token = $this->authenticationManager
                ->authenticate(new BearerToken($access_token));
            $this->securityContext->setToken($token);
        } catch (AuthenticationException $failed) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }
}
