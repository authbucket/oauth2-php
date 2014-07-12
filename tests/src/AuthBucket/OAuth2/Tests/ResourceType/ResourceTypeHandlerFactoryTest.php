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

use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactory;

class ResourceTypeHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testNonExistsResourceTypeHandler()
    {
        $resourceTypeHandlerFactory = new ResourceTypeHandlerFactory(array(
            'foo' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\NonExistsResourceTypeHandler',
        ));
        $resourceTypeHandlerFactory->addResourceTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadAddResourceTypeHandler()
    {
        $resourceTypeHandlerFactory = new ResourceTypeHandlerFactory(array(
            'foo' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\FooResourceTypeHandler',
        ));
        $resourceTypeHandlerFactory->addResourceTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadGetResourceTypeHandler()
    {
        $resourceTypeHandlerFactory = new ResourceTypeHandlerFactory(array(
            'bar' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\BarResourceTypeHandler',
        ));
        $resourceTypeHandlerFactory->getResourceTypeHandler('foo');
    }

    public function testGoodGetResourceTypeHandler()
    {
        $resourceTypeHandlerFactory = new ResourceTypeHandlerFactory(array(
            'bar' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\BarResourceTypeHandler',
        ));
        $resourceTypeHandlerFactory->getResourceTypeHandler('bar');
    }
}
