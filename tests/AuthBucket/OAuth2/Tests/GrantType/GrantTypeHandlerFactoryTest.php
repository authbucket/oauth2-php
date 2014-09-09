<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\GrantType;

use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactory;
use AuthBucket\OAuth2\Tests\WebTestCase;

class GrantTypeHandlerFactoryTest extends WebTestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testNonExistsGrantTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\NonExistsGrantTypeHandler');
        $factory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            null,
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadAddGrantTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\FooGrantTypeHandler');
        $factory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            null,
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadGetGrantTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\BarGrantTypeHandler');
        $factory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            null,
            $classes
        );
        $handler = $factory->getGrantTypeHandler('foo');
    }

    public function testGoodGetGrantTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\BarGrantTypeHandler');
        $factory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            null,
            $classes
        );
        $handler = $factory->getGrantTypeHandler('bar');
        $this->assertEquals($factory->getGrantTypeHandlers(), $classes);
    }
}
