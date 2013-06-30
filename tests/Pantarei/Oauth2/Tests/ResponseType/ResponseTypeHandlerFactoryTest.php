<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\ResponseType;

use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\Oauth2\ResponseType\ResponseTypeHandlerInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class FooResponseTypeHandler
{
}

class BarResponseTypeHandler implements ResponseTypeHandlerInterface
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
    }
}

class ResponseTypeHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Pantarei\Oauth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadAddResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(array(
            'foo' => 'Pantarei\\Oauth2\\Tests\\ResponseType\\FooResponseTypeHandler',
        ));
        $responseTypeHandlerFactory->addResponseTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(array(
            'bar' => 'Pantarei\\Oauth2\\Tests\\ResponseType\\BarResponseTypeHandler',
        ));
        $responseTypeHandlerFactory->getResponseTypeHandler('foo');
    }

    public function testGoodGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory(array(
            'bar' => 'Pantarei\\Oauth2\\Tests\\ResponseType\\BarResponseTypeHandler',
        ));
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
    }
}
