<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\GrantType;

use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class FooGrantTypeHandler
{
}

class BarGrantTypeHandler implements GrantTypeHandlerInterface
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

class GrantTypeHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBadAddGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new FooGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('foo', $grantTypeHandler);
    }

    public function testGoodAddGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new BarGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('foo', $grantTypeHandler);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new BarGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('bar', $grantTypeHandler);
        $grantTypeHandlerFactory->getGrantTypeHandler('foo');
    }

    public function testGoodGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new BarGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('bar', $grantTypeHandler);
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
    }

    public function testBadRemoveGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new BarGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('bar', $grantTypeHandler);
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
        $grantTypeHandlerFactory->removeGrantTypeHandler('foo');
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testGoodRemoveGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory();
        $grantTypeHandler = new BarGrantTypeHandler();
        $grantTypeHandlerFactory->addGrantTypeHandler('bar', $grantTypeHandler);
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
        $grantTypeHandlerFactory->removeGrantTypeHandler('bar');
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
    }
}
