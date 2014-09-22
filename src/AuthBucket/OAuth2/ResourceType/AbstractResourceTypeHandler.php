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

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Shared resource type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractResourceTypeHandler implements ResourceTypeHandlerInterface
{
    protected $httpKernel;
    protected $modelManagerFactory;

    public function __construct(
        HttpKernelInterface $httpKernel,
        ModelManagerFactoryInterface $modelManagerFactory
    ) {
        $this->httpKernel = $httpKernel;
        $this->modelManagerFactory = $modelManagerFactory;
    }
}
