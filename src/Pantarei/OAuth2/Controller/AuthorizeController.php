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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactoryInterface;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 authorization endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizeController
{
    protected $securityContext;
    protected $modelManagerFactory;
    protected $responseTypeHandlerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }

    public function indexAction(Request $request)
    {
        // Fetch response_type from GET.
        $response_type = $this->getResponseType($request);

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory->getResponseTypeHandler($response_type)->handle(
            $this->securityContext,
            $request,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory
        );
    }

    private function getResponseType(Request $request)
    {
        // Validate and set response_type.
        $response_type = $request->query->get('response_type');
        $query = array(
            'response_type' => $response_type
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $response_type;
    }
}
