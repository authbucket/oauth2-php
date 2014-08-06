<?php

/**
 * This file is part of the authbucket/oauth2 package.
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
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $this->app['authbucket_oauth2.user_provider'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\NonExistsGrantTypeHandler')
        );
        $grantTypeHandlerFactory->addGrantTypeHandler('foo', $grantTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadAddGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $this->app['authbucket_oauth2.user_provider'],
            array('foo' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\FooGrantTypeHandler')
        );
        $grantTypeHandlerFactory->addGrantTypeHandler('foo', $grantTypeHandler);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $this->app['authbucket_oauth2.user_provider'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\BarGrantTypeHandler')
        );
        $grantTypeHandlerFactory->getGrantTypeHandler('foo');
    }

    public function testGoodGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(
            $this->app['security'],
            $this->app['security.user_checker'],
            $this->app['security.encoder_factory'],
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $this->app['authbucket_oauth2.token_handler.factory'],
            $this->app['authbucket_oauth2.user_provider'],
            array('bar' => 'AuthBucket\\OAuth2\\Tests\\GrantType\\BarGrantTypeHandler')
        );
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
    }
}
