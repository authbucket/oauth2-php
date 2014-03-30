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
use AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException;
use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use AuthBucket\OAuth2\Util\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * OAuth2 token endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenController
{
    protected $securityContext;
    protected $modelManagerFactory;
    protected $grantTypeHandlerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        UserCheckerInterface $userChecker,
        EncoderFactoryInterface $encoderFactory,
        ModelManagerFactoryInterface $modelManagerFactory,
        GrantTypeHandlerFactoryInterface $grantTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        UserProviderInterface $userProvider = null
    )
    {
        $this->securityContext = $securityContext;
        $this->userChecker = $userChecker;
        $this->encoderFactory = $encoderFactory;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->grantTypeHandlerFactory = $grantTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->userProvider = $userProvider;
    }

    public function tokenAction(Request $request)
    {
        // Fetch grant_type from POST.
        $grant_type = $this->getGrantType($request);

        // Handle token endpoint response.
        return $this->grantTypeHandlerFactory->getGrantTypeHandler($grant_type)->handle(
            $this->securityContext,
            $this->userChecker,
            $this->encoderFactory,
            $request,
            $this->modelManagerFactory,
            $this->tokenTypeHandlerFactory,
            $this->userProvider
        );
    }

    private function getGrantType(Request $request)
    {
        // grant_type must set and in valid format.
        $grant_type = $request->request->get('grant_type');
        $query = array(
            'grant_type' => $grant_type
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $grant_type;
    }
}
