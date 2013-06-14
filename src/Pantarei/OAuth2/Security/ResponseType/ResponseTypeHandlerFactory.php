<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\ResponseType;

use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;

class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    private $responseTypeHandlers;

    public function __construct(array $responseTypeHandlers)
    {
        $this->responseTypeHandlers = $responseTypeHandlers;
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
}
