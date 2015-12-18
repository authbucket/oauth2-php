<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessTokenToken;
use AuthBucket\OAuth2\Validator\Constraints\GrantType;
use AuthBucket\OAuth2\Validator\Constraints\ResponseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * OAuth2 endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2Controller
{
    protected $tokenStorage;
    protected $validator;
    protected $modelManagerFactory;
    protected $responseTypeHandlerFactory;
    protected $grantTypeHandlerFactory;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        GrantTypeHandlerFactoryInterface $grantTypeHandlerFactory
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->grantTypeHandlerFactory = $grantTypeHandlerFactory;
    }

    public function authorizeAction(Request $request)
    {
        // Fetch response_type from GET.
        $responseType = $request->query->get('response_type');
        $errors = $this->validator->validate($responseType, [
            new NotBlank(),
            new ResponseType(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory
            ->getResponseTypeHandler($responseType)
            ->handle($request);
    }

    public function tokenAction(Request $request)
    {
        // Fetch grant_type from POST.
        $grantType = $request->request->get('grant_type');
        $errors = $this->validator->validate($grantType, [
            new NotBlank(),
            new GrantType(),
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => 'The request includes an invalid parameter value.',
            ]);
        }

        // Handle token endpoint response.
        return $this->grantTypeHandlerFactory
            ->getGrantTypeHandler($grantType)
            ->handle($request);
    }

    public function debugAction(Request $request)
    {
        // Fetch authenticated access token from security context.
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token instanceof AccessTokenToken) {
            throw new ServerErrorException([
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ]);
        }

        // Handle debug endpoint response.
        $parameters = [
            'access_token' => $token->getAccessToken(),
            'token_type' => $token->getTokenType(),
            'client_id' => $token->getClientId(),
            'username' => $token->getUsername(),
            'expires' => $token->getExpires()->getTimestamp(),
            'scope' => $token->getScope(),
        ];

        return JsonResponse::create($parameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }
}
