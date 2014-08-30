<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Model\InMemory;

use AuthBucket\OAuth2\Model\AccessTokenInterface;

/**
 * AccessToken
 */
class AccessToken implements AccessTokenInterface
{
    protected $id;

    protected $accessToken;

    protected $tokenType;

    protected $clientId;

    protected $username;

    protected $expires;

    protected $scope;

    public function __construct()
    {
        static $id = 0;

        $this->id = $id;

        $id++;
    }

    public function getId()
    {
        return $this->id;
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

    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
