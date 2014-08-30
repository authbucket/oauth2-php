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
use AuthBucket\OAuth2\TokenType\MacTokenTypeHandler;
use Symfony\Component\HttpFoundation\Request;

class MacTokenTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\TemporarilyUnavailableException
     */
    public function testExceptionGetAccessToken()
    {
        $request = new Request();
        $handler = new MacTokenTypeHandler(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory']
        );
        $handler->getAccessToken($request);
    }

    /**
     * @expectedException \AuthBucket\OAuth2\Exception\TemporarilyUnavailableException
     */
    public function testExceptionCreateAccessToken()
    {
        $modelManagerFactory = new BarModelManagerFactory();
        $handler = new MacTokenTypeHandler(
            $this->app['validator'],
            $this->app['authbucket_oauth2.model_manager.factory']
        );
        $handler->createAccessToken($modelManagerFactory, 'foo');
    }
}
