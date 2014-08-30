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
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\NonExistsResponseTypeHandler')
        );
        $responseTypeHandlerFactory->addResponseTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadAddResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\FooResponseTypeHandler')
        );
        $responseTypeHandlerFactory->addResponseTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler')
        );
        $responseTypeHandlerFactory->getResponseTypeHandler('foo');
    }

    public function testGoodGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(
            $this->app['security'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\ResponseType\\BarResponseTypeHandler')
        );
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
    }
}
