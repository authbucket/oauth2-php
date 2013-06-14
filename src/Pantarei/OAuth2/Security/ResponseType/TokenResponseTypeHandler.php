<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\ResponseType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerInterface;

/**
 * Token response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseTypeHandler extends AbstractResponseTypeHandler
{
    private function generateParameters(
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $client_id,
        $redirect_uri,
        $username = '',
        $scope = array(),
        $state = null
    )
    {
        return $tokenTypeHandlerFactory->getTokenTypeHandler()->createToken(
            $modelManagerFactory,
            $client_id,
            $username,
            $scope,
            $state,
            $withRefreshToken = false
        );
    }
}
