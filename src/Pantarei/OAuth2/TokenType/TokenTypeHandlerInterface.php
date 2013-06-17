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

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

interface TokenTypeHandlerInterface
{
    /**
     * Proxy for listener's handle().
     */
    public function getAccessToken(Request $request);

    /**
     * Proxy for listener's setResponse().
     */
    public function createAccessToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    );
}
