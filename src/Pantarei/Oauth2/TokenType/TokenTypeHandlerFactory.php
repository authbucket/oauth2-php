<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\TokenType;

use Pantarei\Oauth2\Exception\ServerErrorException;

/**
 * Oauth2 grant type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenTypeHandlerFactory implements TokenTypeHandlerFactoryInterface
{
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new ServerErrorException();
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('Pantarei\\Oauth2\\TokenType\\TokenTypeHandlerInterface')) {
                throw new ServerErrorException();
            }
        }

        $this->classes = $classes;
    }

    public function getTokenTypeHandler($type = null)
    {
        if ($type === null) {
            if (count($this->classes) < 1) {
                throw new ServerErrorException();
            }

            $handler = null;
            foreach ($this->classes as $class) {
                $handler = new $class;
                break;
            }
            return $handler;
        }

        if (!isset($this->classes[$type])) {
            throw new ServerErrorException();
        }

        return new $this->classes[$type];
    }
}
