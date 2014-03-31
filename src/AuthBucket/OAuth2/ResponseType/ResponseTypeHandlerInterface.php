<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResponseType;

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 response type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResponseTypeHandlerInterface
{
    /**
     * Handle corresponding response type logic.
     *
     * @param SecurityContextInterface         $securityContext
     *                                                                  The security object that hold the current live token.
     * @param Request                          $request
     *                                                                  Incoming request object.
     * @param ModelManagerFactoryInterface     $modelManagerFactory
     *                                                                  Model manager factory for compare with database record.
     * @param TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
     *                                                                  Token type handler that will generate the correct response
     *                                                                  parameters.
     *
     * @return RedirectResponse
     *                          The redirect response object for authorize endpoint.
     */
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    );
}
