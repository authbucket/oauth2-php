<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\TokenType;

use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

interface ResponseTypeHandlerInterface
{
    /**
     * Proxy for listener's handle().
     */
    public function handle(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        TokenTypeHandlerInterface $tokenTypeHandler,
        array $modelManagers,
        $providerKey
    );
}
