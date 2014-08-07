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
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Validator\Constraints\ResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 authorization endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizeController
{
    protected $validator;
    protected $responseTypeHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory
    )
    {
        $this->validator = $validator;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
    }

    public function authorizeAction(Request $request)
    {
        // Fetch response_type from GET.
        $responseType = $request->query->get('response_type');
        $errors = $this->validator->validateValue($responseType, array(
            new NotBlank(),
            new ResponseType(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory
            ->getResponseTypeHandler($responseType)
            ->handle($request);
    }
}
