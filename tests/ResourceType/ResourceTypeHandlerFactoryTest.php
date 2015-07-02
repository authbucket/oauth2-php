<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\ResourceType;

use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactory;
use AuthBucket\OAuth2\Tests\WebTestCase;

class ResourceTypeHandlerFactoryTest extends WebTestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testNonExistsResourceTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\NonExistsResourceTypeHandler');
        $factory = new ResourceTypeHandlerFactory(
            $this->app,
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadAddResourceTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\FooResourceTypeHandler');
        $factory = new ResourceTypeHandlerFactory(
            $this->app,
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadGetResourceTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\BarResourceTypeHandler');
        $factory = new ResourceTypeHandlerFactory(
            $this->app,
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
        $handler = $factory->getResourceTypeHandler('foo');
    }

    public function testGoodGetResourceTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResourceType\\BarResourceTypeHandler');
        $factory = new ResourceTypeHandlerFactory(
            $this->app,
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
        $handler = $factory->getResourceTypeHandler('bar');
        $this->assertEquals($factory->getResourceTypeHandlers(), $classes);
    }
}
