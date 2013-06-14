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

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;

interface TokenTypeHandlerInterface
{
    /**
     * Proxy for listener's handle().
     */
    public function handle(
        SecurityContextInterface $securityContext,
        GetResponseEvent $event,
        ModelManagerFactoryInterface $modelManagerFactory
    );

    /**
     * Proxy for listener's setResponse().
     */
    public function createToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    );
}
