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

use PantaRei\OAuth2\Model\ClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 ClientToken for token endpoint authentication.
 */
class ClientToken extends AbstractToken
{
    protected $client;
    protected $client_secret;

    public function __construct($client_id, $client_secret, array $roles = array())
    {
        parent::__construct($roles);

        $this->setClient($client_id);
        $this->client_secret = $client_secret;

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
}
