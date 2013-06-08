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

use Pantarei\OAuth2\Model\AccessTokenInterface;
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

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        return $this;
    }

    public function getAccessToken()
    {
        if ($this->access_token instanceof AccessTokenInterface) {
            return $this->access_token->getAccessToken();
        }

        return (string) $this->access_token;
    }

    public function getCredentials()
    {
        return '';
    }
}
