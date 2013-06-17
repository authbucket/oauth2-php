<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\TokenType;

use Pantarei\OAuth2\Exception\ServerErrorException;

class TokenTypeHandlerFactory implements TokenTypeHandlerFactoryInterface
{
    private $tokenTypeHandlers;

    public function __construct()
    {
        $this->tokenTypeHandlers = array();
    }

    public function addTokenTypeHandler($type, $handler)
    {
        if (!$handler instanceof TokenTypeHandlerInterface) {
            throw new ServerErrorException();
        }

        $this->tokenTypeHandlers[$type] = $handler;
    }

    public function getTokenTypeHandler($type = null)
    {
        if ($type !== null) {
            if (!isset($this->tokenTypeHandlers[$type])) {
                throw new ServerErrorException();
            }
            $tokenTypeHandler = $this->tokenTypeHandlers[$type];
        } else {
            foreach ($this->tokenTypeHandlers as $handler) {
                $tokenTypeHandler = $handler;
                break;
            }
        }

        if (!$tokenTypeHandler instanceof TokenTypeHandlerInterface) {
            throw new ServerErrorException();
        }

        return $tokenTypeHandler;
    }

    public function removeTokenTypeHandler($type)
    {
        if (!isset($this->tokenTypeHandlers[$type])) {
            return;
        }

        unset($this->tokenTypeHandlers[$type]);
    }
}
