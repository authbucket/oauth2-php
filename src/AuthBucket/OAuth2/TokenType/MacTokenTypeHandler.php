<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\TokenType;

use AuthBucket\OAuth2\Exception\TemporarilyUnavailableException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * MAC token type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
    {
        throw new TemporarilyUnavailableException(array(
            'error_description' => 'The authorization server is currently unable to handle the request due to a temporary overloading or maintenance of the server.',
        ));
    }

    public function createAccessToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    )
    {
        throw new TemporarilyUnavailableException(array(
            'error_description' => 'The authorization server is currently unable to handle the request due to a temporary overloading or maintenance of the server.',
        ));
    }
}
