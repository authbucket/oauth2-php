<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\TokenType;

use Symfony\Component\HttpFoundation\Request;
use Pantarei\Oauth2\Exception\TemporarilyUnavailableException;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MacTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
    {
        throw new TemporarilyUnavailableException();
    }

    public function createAccessToken(
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
