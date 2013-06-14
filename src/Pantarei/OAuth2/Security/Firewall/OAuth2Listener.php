<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\Firewall;

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Pantarei\OAuth2\Security\Authentication\Token\ClientToken;
use Pantarei\OAuth2\Security\GrantType\GrantTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Security\ResponseType\ResponseTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * TokenEndpointListener implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2Listener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;
    private $httpUtils;
    private $providerKey;
    private $modelManagerFactory;
    private $responseTypeHandlerFactory;
    private $grantTypeHandlerFactory;
    private $tokenTypeHandlerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        HttpUtils $httpUtils,
        ModelManagerFactoryInterface $modelManagerFactory,
        ResponseTypeHandlerFactoryInterface $responseTypeHandlerFactory,
        GrantTypeHandlerFactoryInterface $grantTypeHandlerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        $providerKey,
        array $options
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->httpUtils = $httpUtils;
        $this->providerKey = $providerKey;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->responseTypeHandlerFactory = $responseTypeHandlerFactory;
        $this->grantTypeHandlerFactory = $grantTypeHandlerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->options = array_merge(array(
            'authorize_path' => '/authorize',
            'token_path' => '/token',
        ), $options);
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        /*
        if ($this->httpUtils->checkRequestPath($request, $this->options['authorize_path'])) {
            // Authorization endpoint require a valid UsernamePasswordToken.
            $token = $this->securityContext->getToken();
            if ($token !== null && $token instanceof UsernamePasswordToken && $token->isAuthenticated()) {
                // Fetch response_type from GET.
                $response_type = $this->getResponseType($request);

                // Handle authorization endpoint response.
                $this->ResponseTypeHandlers[$response_type]->handle(
                    $this->securityContext,
                    $this->authenticationManager,
                    $event,
                    $this->tokenTypeHandlerFactory,
                    $this->modelManagerFactory,
                    $this->providerKey,
                );
            }
        } else
         */
        if ($this->httpUtils->checkRequestPath($request, $this->options['token_path'])) {
            // Check client credentials from HTTP Basic Auth or POST.
            $this->checkClientCredentials($request);

            // Fetch grant_type from POST.
            $grant_type = $this->getGrantType($request);

            // Handle token endpoint response.
            $this->grantTypeHandlerFactory->getGrantTypeHandler($grant_type)->handle(
                $this->securityContext,
                $this->authenticationManager,
                $event,
                $this->modelManagerFactory,
                $this->tokenTypeHandlerFactory,
                $this->providerKey
            );
        } else {
            // Handle resource endpoint validation.
            $this->tokenTypeHandlerFactory->getTokenTypeHandler()->handle(
                $this->securityContext,
                $event,
                $this->modelManagerFactory
            );
        }
    }

    private function getResponseType(Request $request)
    {
        // response_type should NEVER come from POST.
        if ($request->request->get('response_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set response_type.
        $response_type = $request->request->get('response_type');
        $query = array('response_type' => $response_type);
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        return $response_type;
    }

    private function getGrantType(Request $request)
    {
        // grant_type should NEVER come from GET.
        if ($request->query->get('grant_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set grant_type.
        $grant_type = $request->request->get('grant_type');
        $query = array('grant_type' => $grant_type);
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        return $grant_type;
    }

    private function checkClientCredentials(Request $request)
    {
        // At least one (and only one) of client credentials method required.
        if (!$request->headers->get('PHP_AUTH_USER', false) && !$request->request->get('client_id', false)) {
            throw new InvalidRequestException();
        } elseif ($request->headers->get('PHP_AUTH_USER', false) && $request->request->get('client_id', false)) {
            throw new InvalidRequestException();
        }

        // Check with HTTP basic auth if exists.
        if ($request->headers->get('PHP_AUTH_USER', false)) {
            $client_id = $request->headers->get('PHP_AUTH_USER', false);
            $client_secret = $request->headers->get('PHP_AUTH_PW', false);
        } else {
            $client_id = $request->request->get('client_id', false);
            $client_secret = $request->request->get('client_secret', false);
        }

        $clientManager = $this->modelManagerFactory->getModelManager('client');
        $client = $clientManager->findClientByClientId($client_id);
        if ($client === null || $client->getClientSecret() !== $client_secret) {
            throw new InvalidClientException();
        }
    }
}
