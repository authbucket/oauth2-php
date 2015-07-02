<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\TokenType;

/**
 * OAuth2 token type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface TokenTypeHandlerFactoryInterface
{
    /**
     * Gets a stored token type handler.
     *
     * @param string $type Type of token type handler, as refer to RFC6749.
     *
     * @return GrantTypeHandlerInterface The stored token type handler.
     *
     * @throw UnsupportedGrantTypeException If supplied token type not found.
     */
    public function getTokenTypeHandler($type = null);

    /**
     * Get a list of all supported handler.
     *
     * @return array Supported handler in key-value pair.
     */
    public function getTokenTypeHandlers();
}
