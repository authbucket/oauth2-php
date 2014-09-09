<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TokenType;

use AuthBucket\OAuth2\Tests\WebTestCase;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactory;

class TokenTypeHandlerFactoryTest extends WebTestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testNonExistsTokenTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\TokenType\\NonExistsTokenTypeHandler');
        $factory = new TokenTypeHandlerFactory(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadAddTokenTypeHandler()
    {
        $classes = array('foo' => 'AuthBucket\\OAuth2\\Tests\\TokenType\\FooTokenTypeHandler');
        $factory = new TokenTypeHandlerFactory(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\ServerErrorException
     */
    public function testBadGetTokenTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\TokenType\\BarTokenTypeHandler');
        $factory = new TokenTypeHandlerFactory(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
        $handler = $factory->getTokenTypeHandler('foo');
    }

    public function testGoodGetTokenTypeHandler()
    {
        $classes = array('bar' => 'AuthBucket\\OAuth2\\Tests\\TokenType\\BarTokenTypeHandler');
        $factory = new TokenTypeHandlerFactory(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory'],
            $classes
        );
        $handler = $factory->getTokenTypeHandler('bar');
        $this->assertEquals($factory->getTokenTypeHandlers(), $classes);
    }
}
