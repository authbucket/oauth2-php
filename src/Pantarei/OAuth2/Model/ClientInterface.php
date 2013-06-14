<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Model;

interface ClientInterface extends ModelInterface
{
    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId();

    /**
     * Get clientSecret
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Get redirectUri
     *
     * @return string
     */
    public function getRedirectUri();
}
