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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

interface TokenTypeHandlerInterface
{
    /**
     * Proxy for listener's handle().
     */
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        array $modelManagers
    );

    /**
     * Proxy for listener's setResponse().
     */
    public function setResponse(
        GetResponseEvent $event,
        array $modelManagers,
        $client_id,
        $username,
        $scope
    );
}
