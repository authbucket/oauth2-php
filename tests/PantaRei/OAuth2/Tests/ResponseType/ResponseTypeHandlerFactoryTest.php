<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests\ResponseType;

use PantaRei\OAuth2\Model\ModelManagerFactoryInterface;
use PantaRei\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use PantaRei\OAuth2\ResponseType\ResponseTypeHandlerInterface;
use PantaRei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
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
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBadAddResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new FooResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('foo', $responseTypeHandler);
    }

    public function testGoodAddResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new BarResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('foo', $responseTypeHandler);
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testBadGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new BarResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('bar', $responseTypeHandler);
        $responseTypeHandlerFactory->getResponseTypeHandler('foo');
    }

    public function testGoodGetResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new BarResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('bar', $responseTypeHandler);
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
    }

    public function testBadRemoveResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new BarResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('bar', $responseTypeHandler);
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
        $responseTypeHandlerFactory->removeResponseTypeHandler('foo');
    }

    /**
     * @expectedException \PantaRei\OAuth2\Exception\UnsupportedResponseTypeException
     */
    public function testGoodRemoveResponseTypeHandler()
    {
        $responseTypeHandlerFactory = new ResponseTypeHandlerFactory();
        $responseTypeHandler = new BarResponseTypeHandler();
        $responseTypeHandlerFactory->addResponseTypeHandler('bar', $responseTypeHandler);
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
        $responseTypeHandlerFactory->removeResponseTypeHandler('bar');
        $responseTypeHandlerFactory->getResponseTypeHandler('bar');
    }
}
