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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Security\Authentication\Token\AccessToken;
use Pantarei\OAuth2\Security\Authentication\Token\ClientToken;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * TokenEndpointListener implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceListener implements ListenerInterface
{
    protected $securityContext;

    protected $authenticationManager;

    protected $providerKey;

    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $providerKey,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Fetch access_token by token type handler.
        $tokenTypeHandler = $this->tokenTypeHandlerFactory->getTokenTypeHandler();
        $access_token = $tokenTypeHandler->getAccessToken($request);

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof AccessToken
                && $token->isAuthenticated()
                && $token->getAccessToken() === $access_token
            ) {
                return;
            }
        }

        $token = new AccessToken($access_token, $this->providerKey);
        $authenticatedToken = $this->authenticationManager->authenticate($token);
        $this->securityContext->setToken($authenticatedToken);
    }
}
