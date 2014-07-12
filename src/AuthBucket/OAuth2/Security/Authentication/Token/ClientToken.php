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

use AuthBucket\OAuth2\Model\ClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 ClientToken for token endpoint authentication.
 */
class ClientToken extends AbstractToken
{
    protected $client;
    protected $clientSecret;
    protected $providerKey;

    public function __construct($clientId, $clientSecret, $providerKey, array $roles = array())
    {
        parent::__construct($roles);

        $this->setClient($clientId);
        $this->clientSecret = $clientSecret;
        $this->providerKey = $providerKey;

        parent::setAuthenticated(count($roles) > 0);
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }

    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getClientId()
    {
        if ($this->client instanceof ClientInterface) {
            return $this->client->getClientId();
        }

        return (string) $this->client;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getCredentials()
    {
        return '';
    }

    public function serialize()
    {
        return serialize(array($this->client, $this->clientSecret, $this->providerKey, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->client, $this->clientSecret, $this->providerKey, $parentStr) = unserialize($str);
    }
}
