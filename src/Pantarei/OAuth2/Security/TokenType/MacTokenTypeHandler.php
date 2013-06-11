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

use Pantarei\OAuth2\Exception\AccessDeniedException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Security\Authentication\Token\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MacTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        array $modelManagers
    )
    {
        return;
    }

    public function setResponse(
        GetResponseEvent $event,
        array $modelManagers,
        $client_id,
        $username,
        $scope
    )
    {
        return;
    }
}
