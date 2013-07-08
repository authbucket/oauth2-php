<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 AccessToken for resource endpoint authentication.
 */
class AccessToken extends AbstractToken
{
    protected $access_token;

    protected $providerKey;

    public function __construct($access_token, $providerKey, array $roles = array())
    {
        parent::__construct($roles);

        $this->access_token = $access_token;
        $this->providerKey = $providerKey;

        parent::setAuthenticated(count($roles) > 0);
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        return $this;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getCredentials()
    {
        return '';
    }

    public function serialize()
    {
        return serialize(array($this->access_token, $this->providerKey, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->access_token, $this->providerKey, $parentStr) = unserialize($str);
    }
}
