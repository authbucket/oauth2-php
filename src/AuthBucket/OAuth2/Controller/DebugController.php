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

use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Validator\Constraints\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 debug endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugController
{
    protected $validator;
    protected $modelManagerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }

    public function debugAction(Request $request)
    {
        // Fetch access_token, should already validated by firewall.
        $tokenTypeHandler = $this->tokenTypeHandlerFactory->getTokenTypeHandler();
        $accessToken = $tokenTypeHandler->getAccessToken($request);

        // Check access_token with database record, again should already
        // validated by firewall.
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $accessTokenStored = $accessTokenManager->readModelOneBy(array(
            'accessToken' => $accessToken,
        ));

        // Handle debug endpoint response.
        $parameters = array(
            'access_token' => $accessTokenStored->getAccessToken(),
            'token_type' => $accessTokenStored->getTokenType(),
            'client_id' => $accessTokenStored->getClientId(),
            'username' => $accessTokenStored->getUsername(),
            'expires' => $accessTokenStored->getExpires()->getTimestamp(),
            'scope' => $accessTokenStored->getScope(),
        );

        return JsonResponse::create($parameters, 200, array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ));
    }
}
