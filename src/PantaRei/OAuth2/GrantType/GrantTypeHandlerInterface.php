<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\GrantType;

use PantaRei\OAuth2\Model\ModelManagerFactoryInterface;
use PantaRei\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 grant type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeHandlerInterface
{
    /**
     * Handle corresponding grant type logic.
     *
     * @param SecurityContextInterface $securityContext
     *   The security object that hold the current live token.
     * @param Request $request
     *   Incoming request object.
     * @param ModelManagerFactoryInterface $modelManagerFactory
     *   Model manager factory for compare with database record.
     * @param TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
     *   Token type handler that will generate the correct response
     *   parameters.
     *
     * @return JsonResponse
     *   The json response object for token endpoint.
     */
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    );
}
