<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException;

/**
 * OAuth2 response type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseTypeHandlerFactory implements ResponseTypeHandlerFactoryInterface
{
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new UnsupportedResponseTypeException(array(
                    'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
                ));
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('AuthBucket\\OAuth2\\ResponseType\\ResponseTypeHandlerInterface')) {
                throw new UnsupportedResponseTypeException(array(
                    'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
                ));
            }
        }

        $this->classes = $classes;
    }

    public function getResponseTypeHandler($type)
    {
        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new UnsupportedResponseTypeException(array(
                'error_description' => 'The authorization server does not support obtaining an authorization code using this method.',
            ));
        }

        return new $this->classes[$type]();
    }
}
