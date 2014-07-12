<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 AccessToken for resource endpoint authentication.
 */
class AccessToken extends AbstractToken
{
    protected $accessToken;
    protected $providerKey;

    public function __construct($accessToken, $providerKey, array $roles = array())
    {
        parent::__construct($roles);

        $this->accessToken = $accessToken;
        $this->providerKey = $providerKey;

        parent::setAuthenticated(count($roles) > 0);
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getCredentials()
    {
        return '';
    }

    public function serialize()
    {
        return serialize(array($this->accessToken, $this->providerKey, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->accessToken, $this->providerKey, $parentStr) = unserialize($str);
    }
}
