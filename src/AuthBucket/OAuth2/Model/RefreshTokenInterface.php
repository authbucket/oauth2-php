<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model;

interface RefreshTokenInterface extends ModelInterface
{
    /**
     * Set refresh_token
     *
     * @param string $refresh_token
     * @return RefreshToken
     */
    public function setRefreshToken($refresh_token);

    /**
     * Get refresh_token
     *
     * @return string
     */
    public function getRefreshToken();

    /**
     * Set client_id
     *
     * @param string $client_id
     * @return RefreshToken
     */
    public function setClientId($client_id);

    /**
     * Get client_id
     *
     * @return string
     */
    public function getClientId();

    /**
     * Set username
     *
     * @param string $username
     * @return RefreshToken
     */
    public function setUsername($username);

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set expires
     *
     * @param integer $expires
     * @return RefreshToken
     */
    public function setExpires($expires);

    /**
     * Get expires
     *
     * @return integer
     */
    public function getExpires();

    /**
     * Set scope
     *
     * @param array $scope
     * @return RefreshToken
     */
    public function setScope($scope);

    /**
     * Get scope
     *
     * @return array
     */
    public function getScope();
}
