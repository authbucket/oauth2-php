<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Exception\ServerErrorException;

/**
 * OAuth2 resource type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceTypeHandlerFactory implements ResourceTypeHandlerFactoryInterface
{
    protected $classes;

    public function __construct(array $classes = array())
    {
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new ServerErrorException(array(
                    'error_description' => 'The resource type is not supported by the resource server.',
                ));
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('AuthBucket\\OAuth2\\ResourceType\\ResourceTypeHandlerInterface')) {
                throw new ServerErrorException(array(
                    'error_description' => 'The resource type is not supported by the resource server.',
                ));
            }
        }

        $this->classes = $classes;
    }

    public function getResourceTypeHandler($type)
    {
        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new ServerErrorException(array(
                'error_description' => 'The resource type is not supported by the resource server.',
            ));
        }

        return new $this->classes[$type];
    }
}
