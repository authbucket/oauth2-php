<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\ResourceType;

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BarResourceTypeHandler implements ResourceTypeHandlerInterface
{
    public function handle(
        HttpKernelInterface $httpKernel,
        ModelManagerFactoryInterface $modelManagerFactory,
        $accessToken,
        array $options = array()
    )
    {
    }
}
