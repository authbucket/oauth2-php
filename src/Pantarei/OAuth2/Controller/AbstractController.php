<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Controller;

use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

abstract class AbstractController implements ControllerInterface
{
    protected $securityContext;
    protected $modelManagerFactory;
    protected $responseTypeHandlerFactory;
    protected $grantTypeHandlerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        GrantTypeHandlerFactoryInterface $grantTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->grantTypeHandlerFactory = $grantTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }
}
