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
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\Exception\UnsupportedRequestTypeException;
use Pantarei\OAuth2\Security\Authentication\Token\ClientToken;
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
    private $modelManagers;
    private $responseTypeHandlers;
    private $grantTypeHandlers;
    private $tokenTypeHandler;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        HttpUtils $httpUtils,
        $providerKey,
        array $modelManagers,
        array $responseTypeHandlers,
        array $grantTypeHandlers,
        $tokenTypeHandler,
        array $options
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->httpUtils = $httpUtils;
        $this->providerKey = $providerKey;
        $this->modelManagers = $modelManagers;
        $this->responseTypeHandlers = $responseTypeHandlers;
        $this->grantTypeHandlers = $grantTypeHandlers;
        $this->tokenTypeHandler = $tokenTypeHandler;
        $this->options = array_merge(array(
            'authorize_path' => '/authorize',
            'token_path' => '/token',
        ), $options);
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        /*
        if ($this->requestAuthorize($request)) {
            // Fetch response_type from GET.
            $response_type = $this->getResponseType($request);
            if (!isset($this->ResponseTypeHandlers[$response_type])) {
                throw new UnsupportedResponseTypeException();
            }

            // Handle authorization endpoint response.
            $this->ResponseTypeHandlers[$response_type]->handle(
            );
        } else
         */
        if ($this->requestToken($request)) {
            // Fetch client_id and client_secret from HTTP Basic Auth or POST.
            list($client_id, $client_secret) = $this->getClientCredentials($request);

            // Validate with client's model manager.
            $this->checkClientCredentials($client_id, $client_secret);

            // Fetch grant_type from POST.
            $grant_type = $this->getGrantType($request);
            if (!isset($this->grantTypeHandlers[$grant_type])) {
                throw new UnsupportedGrantTypeException();
            }

            // Handle token endpoint response.
            $this->grantTypeHandlers[$grant_type]->handle(
                $this->authenticationManager,
                $event,
                $this->tokenTypeHandler,
                $this->modelManagers,
                $client_id,
                $this->providerKey
            );
        } else {
            // Handle resource endpoint validation.
            $this->tokenTypeHandler->handle(
                $this->securityContext,
                $request,
                $this->modelManagers
            );
        }
    }

    private function requestAuthorize(Request $request)
    {
        return null !== $token = $this->securityContext->getToken()
            && $token instanceof UsernamePasswordToken
            && $token->isAuthenticated()
            && $this->httpUtils->checkRequestPath($request, $this->options['token_path']);
    }

    private function requestToken(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->options['token_path']);
    }

    private function getClientCredentials(Request $request)
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

        return array($client_id, $client_secret);
    }

    private function checkClientCredentials($client_id, $client_secret)
    {
        $client = $this->modelManagers['client']->findClientByClientId($client_id);
        if ($client === null || $client->getClientSecret() !== $client_secret) {
            throw new InvalidClientException();
        }
    }

    private function getGrantType(Request $request)
    {
        // grant_type should NEVER come from GET.
        if ($request->query->get('grant_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set grant_type.
        $query = array('grant_type' => $request->request->get('grant_type'));
        $filtered_query = ParameterUtils::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }

        return $request->request->get('grant_type');
    }
}
