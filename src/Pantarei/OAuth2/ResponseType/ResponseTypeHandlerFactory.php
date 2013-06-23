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

use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;

/**
 * OAuth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    protected $responseTypeHandlers;

    public function __construct()
    {
        $this->responseTypeHandlers = array();
    }

    public function addResponseTypeHandler($type, ResponseTypeHandlerInterface $handler)
    {
        $this->responseTypeHandlers[$type] = $handler;
    }

    public function getResponseTypeHandler($type)
    {
        if (!isset($this->responseTypeHandlers[$type])) {
            throw new UnsupportedResponseTypeException();
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
