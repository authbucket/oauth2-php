<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model\InMemory;

use AuthBucket\OAuth2\Model\AccessTokenManagerInterface;

/**
 * AccessTokenManager
 */
class AccessTokenManager extends AbstractModelManager implements AccessTokenManagerInterface
{
    public function getClassName()
    {
        return 'AuthBucket\\OAuth2\\Model\\InMemory\\AccessToken';
    }
}
