<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\TokenType;

use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\Tests\WebTestCase;
use Pantarei\Oauth2\TokenType\MacTokenTypeHandler;
use Symfony\Component\HttpFoundation\Request;

class FooModelManagerFactory implements ModelManagerFactoryInterface
{
    public function getModelManager($type)
    {
    }
}

class MacTokenTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\Oauth2\Exception\TemporarilyUnavailableException
     */
    public function testExceptionGetAccessToken()
    {
        $request = new Request();
        $handler = new MacTokenTypeHandler();
        $handler->getAccessToken($request);
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\TemporarilyUnavailableException
     */
    public function testExceptionCreateAccessToken()
    {
        $modelManagerFactory = new FooModelManagerFactory();
        $handler = new MacTokenTypeHandler();
        $handler->createAccessToken($modelManagerFactory, 'foo');
    }
}
