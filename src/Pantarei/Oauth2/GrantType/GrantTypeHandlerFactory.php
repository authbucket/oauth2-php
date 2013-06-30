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
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!class_exists($class) || !is_subclass_of($class, 'Pantarei\\Oauth2\\GrantType\\GrantTypeHandlerInterface')) {
                throw new UnsupportedGrantTypeException();
            }
        }

        $this->classes = $classes;
    }

    public function getGrantTypeHandler($type)
    {
        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedGrantTypeException();
        }

        return new $this->classes[$type];
    }
}
