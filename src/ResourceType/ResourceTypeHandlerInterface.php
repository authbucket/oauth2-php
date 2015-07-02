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

use AuthBucket\OAuth2\Model\AccessTokenInterface;

/**
 * OAuth2 resource type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResourceTypeHandlerInterface
{
    /**
     * Handle corresponding resource type logic.
     *
     * @param string $accessToken Access token for checking.
     * @param array  $options     Additional options for this handler.
     *
     * @return AccessTokenInterface The stored access token with meta information.
     */
    public function handle(
        $accessToken,
        array $options = array()
    );
}
