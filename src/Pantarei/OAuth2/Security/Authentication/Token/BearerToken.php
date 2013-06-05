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
 * OAuth2 BearerToken for resource endpoint authentication.
 */
class BearerToken extends AbstractToken
{
    private $access_token;

    public function __construct($access_token, array $roles = array())
    {
        parent::__construct($roles);

        $this->access_token = $access_token;

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
        return $this->access_token;
    }

    public function eraseCredentials()
    {
        parent::eraseCredentials();

        $this->access_token = null;
    }

    public function serialize()
    {
        return serialize(array($this->access_token, parent::serialize()));
    }

    public function unserialize($serialized)
    {
        list($this->access_token, $parent_string) = unserialize($serialized);
        parent::unserialize($parent_string);
    }
}
