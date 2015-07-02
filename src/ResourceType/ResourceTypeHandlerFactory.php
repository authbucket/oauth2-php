<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * OAuth2 resource type handler factory implemention.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceTypeHandlerFactory implements ResourceTypeHandlerFactoryInterface
{
    protected $httpKernel;
    protected $modelManagerFactory;
    protected $classes;

    public function __construct(
        HttpKernelInterface $httpKernel,
        ModelManagerFactoryInterface $modelManagerFactory,
        array $classes = array()
    ) {
        $this->httpKernel = $httpKernel;
        $this->modelManagerFactory = $modelManagerFactory;

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

    public function getResourceTypeHandler($type = null)
    {
        $type = $type ?: current(array_keys($this->classes));

        if (!isset($this->classes[$type]) || !class_exists($this->classes[$type])) {
            throw new ServerErrorException(array(
                'error_description' => 'The resource type is not supported by the resource server.',
            ));
        }

        $class = $this->classes[$type];

        return new $class(
            $this->httpKernel,
            $this->modelManagerFactory
        );
    }

    public function getResourceTypeHandlers()
    {
        return $this->classes;
    }
}
