<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model;

/**
 * OAuth2 model manager factory interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ModelManagerFactoryInterface
{
    /**
     * Gets a stored model manager.
     *
     * @param string $type Type of model manager.
     *
     * @return ModelManagerInterface The stored model manager.
     *
     * @throw ServerErrorException If supplied model not found.
     */
    public function getModelManager($type);
}
