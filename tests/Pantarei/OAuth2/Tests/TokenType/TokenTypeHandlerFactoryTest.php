<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\TokenType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactory;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerInterface;
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
     * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
     */
    public function testBadAddTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory(array(
            'foo' => 'Pantarei\\OAuth2\\Tests\\TokenType\\FooTokenTypeHandler',
        ));
        $tokenTypeHandlerFactory->addTokenTypeHandler('foo', $tokenTypeHandler);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
     */
    public function testEmptyGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory();
        $tokenTypeHandlerFactory->getTokenTypeHandler();
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
     */
    public function testBadGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory(array(
            'bar' => 'Pantarei\\OAuth2\\Tests\\TokenType\\BarTokenTypeHandler',
        ));
        $tokenTypeHandlerFactory->getTokenTypeHandler('foo');
    }

    public function testGoodGetTokenTypeHandler()
    {
        $tokenTypeHandlerFactory = new TokenTypeHandlerFactory(array(
            'bar' => 'Pantarei\\OAuth2\\Tests\\TokenType\\BarTokenTypeHandler',
        ));
        $tokenTypeHandlerFactory->getTokenTypeHandler('bar');
    }
}
