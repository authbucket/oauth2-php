<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\TokenType;

use Pantarei\OAuth2\Exception\AccessDeniedException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Security\Authentication\Token\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class BearerTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function handle(
        SecurityContextInterface $securityContext,
        Request $request,
        array $modelManagers
    )
    {
        $headers_token = $request->headers->get('Authorization', false);
        if ($headers_token && preg_match('/Bearer\s*([^\s]+)/', $headers_token, $matches)) {
            $headers_token = $matches[1];
        } else {
            $headers_token = false;
        }

        $request_token = $request->request->get('access_token', false);
        $query_token = $request->query->get('access_token', false);

        // At least one (and only one) of client credentials method required.
        if (!$headers_token && !$request_token && !$query_token) {
            throw new InvalidRequestException();
        } elseif (($headers_token && $request_token)
            || ($request_token && $query_token)
            || ($query_token && $headers_token)) {
                throw new InvalidRequestException();
            }

        // Check with HTTP basic auth if exists.
        if ($headers_token) {
            $access_token = $headers_token;
        } elseif ($request_token) {
            $access_token = $request_token;
        } elseif ($query_token) {
            $access_token = $query_token;
        }

        if (null !== $token = $securityContext->getToken()) {
            if ($token instanceof AccessToken
                && $token->isAuthenticated()
                && $token->getCredentials() === $access_token) {
                    return;
                }
        }

        try {
            if (null === $modelManagers['access_token']->findAccessTokenByAccessToken($access_token)) {
                throw new AccessDeniedException();
            }
            $securityContext->setToken(new AccessToken($access_token));
        } catch (AccessDeniedException $failed) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }

    public function setResponse(
        GetResponseEvent $event,
        array $modelManagers,
        $client_id,
        $username,
        $scope
    )
    {
        $access_token = $modelManagers['access_token']->createAccessToken();
        $access_token->setAccessToken(md5(uniqid(null, true)))
            ->setTokenType('bearer')
            ->setClientId($client_id)
            ->setUsername($username)
            ->setExpires(time() + 3600)
            ->setScope($scope);
        $modelManagers['access_token']->updateAccessToken($access_token);

        $refresh_token = $modelManagers['refresh_token']->createRefreshToken();
        $refresh_token->setRefreshToken(md5(uniqid(null, true)))
            ->setClientId($client_id)
            ->setUsername($username)
            ->setExpires(time() + 86400)
            ->setScope($scope);
        $modelManagers['refresh_token']->updateRefreshToken($refresh_token);

        $parameters = array(
            'access_token' => $access_token->getAccessToken(),
            'token_type' => $access_token->getTokenType(),
            'expires_in' => $access_token->getExpires() - time(),
            'refresh_token' => $refresh_token->getRefreshToken(),
            'scope' => implode(' ', $scope),
        );
        $headers = array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );

        $response = JsonResponse::create(array_filter($parameters), 200, $headers);
        $event->setResponse($response);
    }
}
