<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 resource type handler interface.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResourceTypeHandlerInterface
{
    /**
     * Handle corresponding resource type logic.
     *
     * @param SecurityContextInterface         $securityContext         The security object that hold the current live token.
     * @param Request                          $request                 Incoming request object.
     * @param ModelManagerFactoryInterface     $modelManagerFactory     Model manager factory for compare with database record.
     * @param TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory Token type handler that will generate the correct resource parameters.
     *
     * @return RedirectResponse The redirect resource object for authorize endpoint.
     */
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    );
}
