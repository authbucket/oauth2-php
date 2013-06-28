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
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactory;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class FooTokenTypeHandler
{
}

class BarTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
    {
    }


    public function createAccessToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    )
    {
    }
}

class TokenTypeHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBadAddTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new FooTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('foo', $tokenTypeHandler);
    }

    public function testGoodAddTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new BarTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('foo', $tokenTypeHandler);
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\ServerErrorException
     */
    public function testEmptyGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandlerFactory->getTokenTypeHandler();
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\ServerErrorException
     */
    public function testBadGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new BarTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('bar', $tokenTypeHandler);
        $tokenTypeHandlerFactory->getTokenTypeHandler('foo');
    }

    public function testGoodGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new BarTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('bar', $tokenTypeHandler);
        $tokenTypeHandlerFactory->getTokenTypeHandler('bar');
    }

    public function testBadRemoveTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new BarTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('bar', $tokenTypeHandler);
        $tokenTypeHandlerFactory->getTokenTypeHandler('bar');
        $tokenTypeHandlerFactory->removeTokenTypeHandler('foo');
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\ServerErrorException
     */
    public function testGoodRemoveTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandler = new BarTokenTypeHandler();
        $tokenTypeHandlerFactory->addTokenTypeHandler('bar', $tokenTypeHandler);
        $tokenTypeHandlerFactory->getTokenTypeHandler('bar');
        $tokenTypeHandlerFactory->removeTokenTypeHandler('bar');
        $tokenTypeHandlerFactory->getTokenTypeHandler('bar');
    }
}
