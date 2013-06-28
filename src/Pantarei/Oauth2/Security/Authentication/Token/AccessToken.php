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

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Oauth2 AccessToken for resource endpoint authentication.
 */
class AccessToken extends AbstractToken
{
    protected $access_token;

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
        return $this->access_token;
    }

    public function getCredentials()
    {
        return '';
    }
}
