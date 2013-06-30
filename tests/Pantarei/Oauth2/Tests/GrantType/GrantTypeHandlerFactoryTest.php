<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\GrantType;

use Pantarei\Oauth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\Oauth2\GrantType\GrantTypeHandlerInterface;
use Pantarei\Oauth2\Model\ModelManagerFactoryInterface;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FooGrantTypeHandler
{
}

class BarGrantTypeHandler implements GrantTypeHandlerInterface
{
    public function handle(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
    }
}

class GrantTypeHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Pantarei\Oauth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadAddGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(array(
            'foo' => 'Pantarei\\Oauth2\\Tests\\GrantType\\FooGrantTypeHandler',
        ));
        $grantTypeHandlerFactory->addGrantTypeHandler('foo', $grantTypeHandler);
    }

    /**
     * @expectedException \Pantarei\Oauth2\Exception\UnsupportedGrantTypeException
     */
    public function testBadGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(array(
            'bar' => 'Pantarei\\Oauth2\\Tests\\GrantType\\BarGrantTypeHandler',
        ));
        $grantTypeHandlerFactory->getGrantTypeHandler('foo');
    }

    public function testGoodGetGrantTypeHandler()
    {
        $grantTypeHandlerFactory = new GrantTypeHandlerFactory(array(
            'bar' => 'Pantarei\\Oauth2\\Tests\\GrantType\\BarGrantTypeHandler',
        ));
        $grantTypeHandlerFactory->getGrantTypeHandler('bar');
    }
}
