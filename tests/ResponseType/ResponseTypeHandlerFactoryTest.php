<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\ResponseType;

use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use AuthBucket\OAuth2\Tests\WebTestCase;

class ResponseTypeHandlerFactoryTest extends WebTestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testNonExistsResponseTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\NonExistsResponseTypeHandler');
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadAddResponseTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\FooResponseTypeHandler');
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadGetResponseTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler');
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $classes
        );
        $handler = $factory->getResponseTypeHandler('foo');
    }

    public function testGoodGetResponseTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler');
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $classes
        );
        $handler = $factory->getResponseTypeHandler('bar');
        $this->assertEquals($factory->getResponseTypeHandlers(), $classes);
    }
}
