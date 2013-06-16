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
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerFactoryInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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
    private $httpUtils;
    private $modelManagerFactory;
    private $tokenTypeHandlerFactory;
    private $options;

    public function __construct(
        SecurityContextInterface $securityContext,
        HttpUtils $httpUtils,
        ModelManagerFactoryInterface $modelManagerFactory,
        TokenTypeHandlerFactoryInterface $tokenTypeHandlerFactory,
        array $options
    )
    {
        $this->securityContext = $securityContext;
        $this->httpUtils = $httpUtils;
        $this->modelManagerFactory = $modelManagerFactory;
        $this->tokenTypeHandlerFactory = $tokenTypeHandlerFactory;
        $this->options = array_merge(array(
            'authorize_path' => '/authorize',
            'token_path' => '/token',
        ), $options);
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->httpUtils->checkRequestPath($request, $this->options['token_path'])) {
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

            if (null !== $token = $this->securityContext->getToken()) {
                if ($token instanceof ClientToken
                    && $token->isAuthenticated()
                    && $token->getClientId() === $client_id
                ) {
                    return;
                }
            }

            try {
                $clientManager = $this->modelManagerFactory->getModelManager('client');
                $client = $clientManager->findClientByClientId($client_id);
                if ($client === null || $client->getClientSecret() !== $client_secret) {
                    throw new InvalidClientException();
                }
                $this->securityContext->setToken(new ClientToken($client_id, $client_secret));
            } catch (Exception $failed) {
                $response = new Response();
                $response->setStatusCode(403);
                $event->setResponse($response);
            }
        } else {
            // Handle resource endpoint validation.
            $this->tokenTypeHandlerFactory->getTokenTypeHandler()->handle(
                $this->securityContext,
                $event,
                $this->modelManagerFactory
            );
        }
    }
}
