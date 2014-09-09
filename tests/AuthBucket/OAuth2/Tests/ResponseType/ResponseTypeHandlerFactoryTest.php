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
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\NonExistsResponseTypeHandler')
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadAddResponseTypeHandler()
    {
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\FooResponseTypeHandler')
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadGetResponseTypeHandler()
    {
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler')
        );
        $factory->getResponseTypeHandler('foo');
    }

    public function testGoodGetResponseTypeHandler()
    {
        $factory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler')
        );
        $factory->getResponseTypeHandler('bar');
    }
}
