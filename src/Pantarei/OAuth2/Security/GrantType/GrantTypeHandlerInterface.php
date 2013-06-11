<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\GrantType;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

interface GrantTypeHandlerInterface
{
    /**
     * Proxy for listener's handle().
     */
    public function handle(
        AuthenticationManagerInterface $authenticationManager,
        GetResponseEvent $event,
        $tokenTypeHandler,
        array $modelManagers,
        $client_id,
        $providerKey
    );
}
