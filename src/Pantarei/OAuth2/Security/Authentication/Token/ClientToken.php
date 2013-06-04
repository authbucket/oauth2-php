<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuth2 ClientToken for token endpoint authentication.
 */
class ClientToken extends AbstractToken
{
    private $client_secret;

    public function __construct($client_id, $client_secret, array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($client_id);
        $this->client_secret = $client_secret;

        parent::setAuthenticated(count($roles) > 0);
    }

    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }

        parent::setAuthenticated(false);
    }

    public function getCredentials()
    {
        return $this->client_secret;
    }

    public function eraseCredentials()
    {
        parent::eraseCredentials();

        $this->client_secret = null;
    }

    public function serialize()
    {
        return serialize(array($this->client_secret, parent::serialize()));
    }

    public function unserialize($serialized)
    {
        list($this->client_secret, $parent_string) = unserialize($serialized);
        parent::unserialize($parent_string);
    }
}
