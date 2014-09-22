<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TokenType;

use AuthBucket\OAuth2\TokenType\TokenTypeHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class BarTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
    {
    }

    public function createAccessToken(
        $clientId,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    ) {
    }
}
