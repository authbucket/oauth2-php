<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\ResponseType;

use Pantarei\Oauth2\Exception\UnsupportedResponseTypeException;

/**
 * Oauth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!class_exists($class) || !is_subclass_of($class, 'Pantarei\\Oauth2\\ResponseType\\ResponseTypeHandlerInterface')) {
                throw new UnsupportedResponseTypeException();
            }
        }

        $this->classes = $classes;
    }

    public function getResponseTypeHandler($type)
    {
        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedResponseTypeException();
        }

        return new $this->classes[$type];
    }
}
