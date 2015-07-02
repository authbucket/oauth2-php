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
 * OAuth2 access token interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface AccessTokenInterface extends ModelInterface
{
    /**
     * Set access_token
     *
     * @param string $accessToken
     *
     * @return AccessToken
     */
    public function setAccessToken($accessToken);

    /**
     * Get access_token
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Set token_type
     *
     * @param string $tokenType
     *
     * @return AccessToken
     */
    public function setTokenType($tokenType);

    /**
     * Get token_type
     *
     * @return string
     */
    public function getTokenType();

    /**
     * Set client_id
     *
     * @param string $clientId
     *
     * @return AccessToken
     */
    public function setClientId($clientId);

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
     *
     * @return AccessToken
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
     *
     * @return AccessToken
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
     *
     * @return AccessToken
     */
    public function setScope($scope);

    /**
     * Get scope
     *
     * @return array
     */
    public function getScope();
}
