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
use AuthBucket\OAuth2\Validator\Constraints\ClientId;
use AuthBucket\OAuth2\Validator\Constraints\RedirectUri;
use AuthBucket\OAuth2\Validator\Constraints\ResponseType;
use AuthBucket\OAuth2\Validator\Constraints\Scope;
use AuthBucket\OAuth2\Validator\Constraints\State;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * OAuth2 authorization endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizeController
{
    protected $securityContext;
    protected $validator;
    protected $modelManagerFactory;
    protected $responseTypeHandlerFactory;
    protected $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
    }

    public function authorizeAction(Request $request)
    {
        // Validate input parameters.
        $parameters = array(
            'client_id' => $request->query->get('client_id'),
            'redirect_uri' => $request->query->get('redirect_uri'),
            'response_type' => $request->query->get('response_type'),
            'scope' => $request->query->get('scope'),
            'state' => $request->query->get('state'),
            'username' => $this->securityContext->getToken()->getUsername(),
        );

        $constraints = new Collection(array(
            'client_id' => array(new NotBlank(), new ClientId()),
            'redirect_uri' => new RedirectUri(),
            'response_type' => array(new NotBlank(), new ResponseType()),
            'scope' => new Scope(),
            'state' => array(new NotBlank(), new State()),
            'username' => array(new NotBlank(), new Username()),
        ));

        $errors = $this->validator->validateValue($parameters, $constraints);
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Handle authorize endpoint response.
        return $this->responseTypeHandlerFactory
            ->getResponseTypeHandler($parameters['response_type'])
            ->handle(
                $request,
                $this->securityContext,
                $this->validator,
                $this->modelManagerFactory,
                $this->tokenTypeHandlerFactory
            );
    }
}
