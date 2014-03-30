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

interface CodeInterface extends ModelInterface
{
    /**
     * Set code
     *
     * @param string $code
     * @return Code
     */
    public function setCode($code);

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set client_id
     *
     * @param string $client_id
     * @return Code
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
     * @return Code
     */
    public function setUsername($username);

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set redirect_uri
     *
     * @param string $redirect_uri
     * @return Code
     */
    public function setRedirectUri($redirect_uri);

    /**
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri();

    /**
     * Set expires
     *
     * @param integer $expires
     * @return Code
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
     * @return Code
     */
    public function setScope($scope);

    /**
     * Get scope
     *
     * @return array
     */
    public function getScope();
}
