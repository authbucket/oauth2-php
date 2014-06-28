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

/**
 * OAuth2 client interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ClientInterface extends ModelInterface
{
    /**
     * Set client_id
     *
     * @param string $client_id
     *
     * @return Client
     */
    public function setClientId($client_id);

    /**
     * Get client_id
     *
     * @return string
     */
    public function getClientId();

    /**
     * Set client_secret
     *
     * @param string $client_secret
     *
     * @return Client
     */
    public function setClientSecret($client_secret);

    /**
     * Get client_secret
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Set redirect_uri
     *
     * @param string $redirect_uri
     *
     * @return Client
     */
    public function setRedirectUri($redirect_uri);

    /**
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri();
}
