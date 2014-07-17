<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
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
    protected $authorizeScopeUri;

    public function __construct(
        SecurityContextInterface $securityContext,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $authorizeScopeUri = null
    )
    {
        $this->securityContext = $securityContext;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->authorizeScopeUri = $authorizeScopeUri;
    }

    public function authorizeAction(Request $request)
    {
        // Fetch response_type from GET.
        $responseType = $this->getResponseType($request);

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory
            ->getResponseTypeHandler($responseType)
            ->handle(
                $this->securityContext,
                $request,
                $this->modelManagerFactory,
                $this->tokenTypeHandlerFactory,
                $this->authorizeScopeUri
            );
    }

    private function getResponseType(Request $request)
    {
        // Validate and set response_type.
        $responseType = $request->query->get('response_type');
        if (!Filter::filter(array('response_type' => $responseType))) {
            throw new InvalidRequestException();
        }

        return $responseType;
    }
}
