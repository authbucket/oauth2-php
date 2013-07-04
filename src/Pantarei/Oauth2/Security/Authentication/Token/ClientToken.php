<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Security\Authentication\Token;

use Pantarei\Oauth2\Model\ClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Oauth2 ClientToken for token endpoint authentication.
 */
class ClientToken extends AbstractToken
{
    protected $client;

    protected $client_secret;

    protected $providerKey;

    public function __construct($client_id, $client_secret, $providerKey, array $roles = array())
    {
        parent::__construct($roles);

        $this->setClient($client_id);
        $this->client_secret = $client_secret;
        $this->providerKey = $providerKey;

        parent::setAuthenticated(count($roles) > 0);
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
        return $this->client_secret;
    }

    public function getCredentials()
    {
        return '';
    }

    public function serialize()
    {
        return serialize(array($this->client_id, $this->client_secret, $this->providerKey, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->client_id, $this->client_secret, $this->providerKey, $parentStr) = unserialize($str);
    }
}
