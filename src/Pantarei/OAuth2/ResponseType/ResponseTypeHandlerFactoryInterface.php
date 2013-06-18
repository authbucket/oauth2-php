<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\ResponseType;

/**
 * OAuth2 response type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResponseTypeHandlerFactoryInterface
{
    /** 
     * Adds a response type handler.
     *
     * @param string $type
     *   Type of response type handler, as refer to RFC6749.
     * @param ResponseTypeHandlerInterface $handler
     *   A response type handler instance.
     */
    public function addResponseTypeHandler($type, ResponseTypeHandlerInterface $handler);

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

    /** 
     * Removes a stored response type handler.
     *
     * @param string $type
     *   Type of response type handler, as refer to RFC6749.
     */
    public function removeResponseTypeHandler($type);
}
