<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

/**
 * OAuth2 grant type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeHandlerFactoryInterface
{
    /**
     * Gets a stored grant type handler.
     *
     * @param string $type Type of grant type handler, as refer to RFC6749.
     *
     * @return GrantTypeHandlerInterface The stored grant type handler.
     *
     * @throw UnsupportedGrantTypeException If supplied grant type not found.
     */
    public function getGrantTypeHandler($type = null);

    /**
     * Get a list of all supported handler.
     *
     * @return array Supported handler in key-value pair.
     */
    public function getGrantTypeHandlers();
}
