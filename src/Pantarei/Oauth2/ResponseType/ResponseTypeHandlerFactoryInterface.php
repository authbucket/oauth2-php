<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\ResponseType;

/**
 * Oauth2 response type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResponseTypeHandlerFactoryInterface
{
    /**
     * Gets a stored response type handler.
     *
     * @param string $type
     *   Type of response type handler, as refer to RFC6749.
     *
     * @return ResponseTypeHandlerInterface
     *   The stored response type handler.
     *
     * @throw UnsupportedResponseTypeException
     *   If supplied response type not found.
     */
    public function getResponseTypeHandler($type);
}
