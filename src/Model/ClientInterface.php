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
 * OAuth2 client interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ClientInterface extends ModelInterface
{
    /**
     * Set client_id
     *
     * @param string $clientId
     *
     * @return Client
     */
    public function setClientId($clientId);

    /**
     * Get client_id
     *
     * @return string
     */
    public function getClientId();

    /**
     * Set client_secret
     *
     * @param string $clientSecret
     *
     * @return Client
     */
    public function setClientSecret($clientSecret);

    /**
     * Get client_secret
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Set redirect_uri
     *
     * @param string $redirectUri
     *
     * @return Client
     */
    public function setRedirectUri($redirectUri);

    /**
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri();
}
