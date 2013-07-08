<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Model;

interface CodeInterface extends ModelInterface
{
    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId();

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get redirectUri
     *
     * @return string
     */
    public function getRedirectUri();

    /**
     * Get expires
     *
     * @return integer
     */
    public function getExpires();

    /**
     * Get scope
     *
     * @return array
     */
    public function getScope();
}
