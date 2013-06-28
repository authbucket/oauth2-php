<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\GrantType;

use Pantarei\Oauth2\Exception\UnsupportedGrantTypeException;

/**
 * Oauth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class GrantTypeHandlerFactory implements GrantTypeHandlerFactoryInterface
{
    protected $grantTypeHandlers;

    public function __construct()
    {
        $this->grantTypeHandlers = array();
    }

    public function addGrantTypeHandler($type, GrantTypeHandlerInterface $handler)
    {
        $this->grantTypeHandlers[$type] = $handler;
    }

    public function getGrantTypeHandler($type)
    {
        if (!isset($this->grantTypeHandlers[$type])) {
            throw new UnsupportedGrantTypeException();
        }

        return $this->grantTypeHandlers[$type];
    }

    public function removeGrantTypeHandler($type)
    {
        if (!isset($this->grantTypeHandlers[$type])) {
            return;
        }

        unset($this->grantTypeHandlers[$type]);
    }
}
