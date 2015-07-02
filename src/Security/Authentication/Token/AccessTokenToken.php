<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Authentication\Token;

use AuthBucket\OAuth2\Model\AccessTokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 AccessTokenToken for resource endpoint authentication.
 */
class AccessTokenToken extends AbstractToken implements AccessTokenInterface
{
    protected $providerKey;

    protected $accessToken;
    protected $tokenType;
    protected $clientId;
    protected $username;
    protected $expires;
    protected $scope;

    public function __construct(
        $providerKey,
        $accessToken,
        $tokenType = '',
        $clientId = '',
        $username = '',
        $expires = '',
        array $scope = array(),
        array $roles = array()
    ) {
        parent::__construct($roles);

        $this->providerKey = $providerKey;

        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->clientId = $clientId;
        $this->username = $username;
        $this->expires = $expires;
        $this->scope = $scope;

        parent::setAuthenticated(count($roles) > 0);
    }

    public function getId()
    {
        return 0;
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

    public function getProviderKey()
    {
        return $this->providerKey;
    }

    public function getCredentials()
    {
        return '';
    }

    public function serialize()
    {
        return serialize(array(
            $this->providerKey,
            $this->accessToken,
            $this->tokenType,
            $this->clientId,
            $this->username,
            $this->expires,
            $this->scope,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->providerKey,
            $this->accessToken,
            $this->tokenType,
            $this->clientId,
            $this->username,
            $this->expires,
            $this->scope,
            $parentStr
        ) = unserialize($str);
    }
}
