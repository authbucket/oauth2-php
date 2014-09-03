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

/**
 * Shared resource type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class AbstractResourceTypeHandler implements ResourceTypeHandlerInterface
{
    protected $modelManagerFactory;

    public function __construct(
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->modelManagerFactory = $modelManagerFactory;
    }
}
