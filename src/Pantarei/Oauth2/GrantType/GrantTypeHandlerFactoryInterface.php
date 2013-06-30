<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\GrantType;

/**
 * Oauth2 grant type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeHandlerFactoryInterface
{
    /**
     * Gets a stored grant type handler.
     *
     * @param string $type
     *   Type of grant type handler, as refer to RFC6749.
     *
     * @return GrantTypeHandlerInterface
     *   The stored grant type handler.
     *
     * @throw UnsupportedGrantTypeException
     *   If supplied grant type not found.
     */
    public function getGrantTypeHandler($type);
}
