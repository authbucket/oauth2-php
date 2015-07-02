<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

/**
 * OAuth2 resource type handler factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResourceTypeHandlerFactoryInterface
{
    /**
     * Gets a stored resource type handler.
     *
     * @param string $type Type of resource type handler.
     *
     * @return ResourceTypeHandlerInterface The stored resource type handler.
     *
     * @throw ServerErrorException If supplied resource type not found.
     */
    public function getResourceTypeHandler($type = null);

    /**
     * Get a list of all supported handler.
     *
     * @return array Supported handler in key-value pair.
     */
    public function getResourceTypeHandlers();
}
