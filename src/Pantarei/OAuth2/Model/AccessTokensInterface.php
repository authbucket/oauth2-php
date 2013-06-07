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

use Symfony\Component\Security\Core\User\UserInterface;

interface AccessTokensInterface extends UserInterface
{
    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Get tokenType
     *
     * @return string
     */
    public function getTokenType();

    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId();

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
