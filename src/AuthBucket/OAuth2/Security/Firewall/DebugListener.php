<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Firewall;

use AuthBucket\OAuth2\Security\Authentication\Token\AccessToken;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * DebugListener implements OAuth2 debug endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugListener implements ListenerInterface
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
        $accessToken = $tokenTypeHandler->getAccessToken($request);

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof AccessToken
                && $token->isAuthenticated()
                && $token->getAccessToken() === $accessToken
            ) {
                return;
            }
        }

        $token = new AccessToken($accessToken, $this->providerKey);
        $authenticatedToken = $this->authenticationManager->authenticate($token);
        $this->securityContext->setToken($authenticatedToken);
    }
}
