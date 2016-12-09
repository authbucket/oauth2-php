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
 * OAuth2 user interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface UserInterface extends ModelInterface
{
    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username);

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername();
}
