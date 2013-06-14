<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\GrantType;

use Pantarei\OAuth2\Exception\ServerErrorException;

class GrantTypeHandlerFactory implements GrantTypeHandlerFactoryInterface
{
    private $grantTypeHandlers;

    public function __construct(array $grantTypeHandlers)
    {
        $this->grantTypeHandlers = $grantTypeHandlers;
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
}
