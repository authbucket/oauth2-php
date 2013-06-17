<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\ResponseType;

use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;

class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    private $responseTypeHandlers;

    public function __construct()
    {
        $this->responseTypeHandlers = array();
    }

    public function addResponseTypeHandler($type, $handler)
    {
        if (!$handler instanceof ResponseTypeHandlerInterface) {
            throw new ServerErrorException();
        }

        $this->responseTypeHandlers[$type] = $handler;
    }

    public function getResponseTypeHandler($type)
    {
        if (!isset($this->responseTypeHandlers[$type])) {
            throw new UnsupportedResponseTypeException();
        }

        if (!$this->responseTypeHandlers[$type] instanceof ResponseTypeHandlerInterface) {
            throw new ServerErrorException();
        }

        return $this->responseTypeHandlers[$type];
    }

    public function removeResponseTypeHandler($type)
    {
        if (!isset($this->responseTypeHandlers[$type])) {
            return;
        }

        unset($this->responseTypeHandlers[$type]);
    }
}
