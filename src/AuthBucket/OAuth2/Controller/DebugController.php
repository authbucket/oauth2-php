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
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Validator\Constraints\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
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
        // Fetch adebu_token from GET.
        $tokenTypeHandler = $this->tokenTypeHandlerFactory->getTokenTypeHandler();
        $debugToken = $request->query->get('debug_token')
            ?: $request->request->get('debug_token')
            ?: $tokenTypeHandler->getAccessToken($request);
        $errors = $this->validator->validateValue($debugToken, array(
            new NotBlank(),
            new AccessToken(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Check debug_token with database record.
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $accessToken = $accessTokenManager->readModelOneBy(array(
            'accessToken' => $debugToken,
        ));
        if (null === $accessToken) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request is otherwise malformed.',
            ));
        } elseif ($accessToken->getExpires() < new \DateTime()) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request is otherwise malformed.',
            ));
        }

        // Handle debug endpoint response.
        $parameters = array(
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => $accessToken->getTokenType(),
            'client_id' => $accessToken->getClientId(),
            'username' => $accessToken->getUsername(),
            'expires' => $accessToken->getExpires()->getTimestamp(),
            'scope' => $accessToken->getScope(),
        );

        return JsonResponse::create($parameters);
    }
}
