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
 * ModelManagerInterface in-memory implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenManager implements AccessTokenManagerInterface
{
    use ModelManagerTrait;

    public function getClassName()
    {
        return 'AuthBucket\\OAuth2\\Model\\AccessToken';
    }
}
