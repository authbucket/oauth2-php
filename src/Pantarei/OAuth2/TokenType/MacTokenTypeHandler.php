<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\TokenType;

use Pantarei\OAuth2\Exception\TemporarilyUnavailableException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MacTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function handle(
        SecurityContextInterface $securityContext,
        GetResponseEvent $event,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        throw new TemporarilyUnavailableException();
    }

    public function setResponse(
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    )
    {
        throw new TemporarilyUnavailableException();
    }
}
