<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

use Pantarei\OAuth2\Exception\ServerErrorException;

class GrantTypeHandlerFactory implements GrantTypeHandlerFactoryInterface
{
    private $grantTypeHandlers;

    public function __construct()
    {
        $this->grantTypeHandlers = array();
    }

    public function addGrantTypeHandler($type, $handler)
    {
        if (!$handler instanceof GrantTypeHandlerInterface) {
            throw new ServerErrorException();
        }

        $this->grantTypeHandlers[$type] = $handler;
    }

    public function getGrantTypeHandler($type)
    {
        if (!isset($this->grantTypeHandlers[$type])) {
            throw new ServerErrorException();
        }

        if (!$this->grantTypeHandlers[$type] instanceof GrantTypeHandlerInterface) {
            throw new ServerErrorException();
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
