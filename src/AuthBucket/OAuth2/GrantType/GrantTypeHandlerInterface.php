<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\GrantType;

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\ValidatorInterface;

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
     * @param Request                          $request                 Incoming request object.
     * @param SecurityContextInterface         $securityContext         The security object that hold the current live token.
     * @param UserCheckerInterface             $userChecker             For grant_type = password.
     * @param EncoderFactoryInterface          $encoderFactory          For grant_type = password.
     * @param ValidatorInterface               $validator
     * @param ModelManagerFactoryInterface     $modelManagerFactory     Model manager factory for compare with database record.
     * @param TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory Token type handler that will generate the correct response parameters.
     * @param UserProviderInterface            $userProvider            For grant_type = password.
     *
     * @return JsonResponse The json response object for token endpoint.
     */
    public function handle(
        Request $request,
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    );
}
