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

/**
 * OAuth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenTypeHandlerFactory implements TokenTypeHandlerFactoryInterface
{
    protected $tokenTypeHandlers;

    public function __construct()
    {
        $this->tokenTypeHandlers = array();
    }

    public function addTokenTypeHandler($type, TokenTypeHandlerInterface $handler)
    {
        $this->tokenTypeHandlers[$type] = $handler;
    }

    public function getTokenTypeHandler($type = null)
    {
        if ($type === null) {
            if (count($this->tokenTypeHandlers) < 1) {
                throw new ServerErrorException();
            }

            $tokenTypeHandler = null;
            foreach ($this->tokenTypeHandlers as $handler) {
                $tokenTypeHandler = $handler;
                break;
            }
            return $tokenTypeHandler;
        }

        if (!isset($this->tokenTypeHandlers[$type])) {
            throw new ServerErrorException();
        }

        return $this->tokenTypeHandlers[$type];
    }

    public function removeTokenTypeHandler($type)
    {
        if (!isset($this->tokenTypeHandlers[$type])) {
            return;
        }

        unset($this->tokenTypeHandlers[$type]);
    }
}
