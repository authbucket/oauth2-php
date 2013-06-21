<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\TokenType;

use Pantarei\OAuth2\Exception\AccessDeniedException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bearer token type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
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
            || ($query_token && $headers_token)
        ) {
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

        return $access_token;
    }

    public function createAccessToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $client_id,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    )
    {
        $modelManager = $modelManagerFactory->getModelManager('access_token');
        $access_token = $modelManager->createModel();
        $access_token->setAccessToken(md5(uniqid(null, true)))
            ->setTokenType('bearer')
            ->setClientId($client_id)
            ->setUsername($username)
            ->setExpires(time() + 3600)
            ->setScope($scope);
        $modelManager->updateModel($access_token);

        $parameters = array(
            'access_token' => $access_token->getAccessToken(),
            'token_type' => $access_token->getTokenType(),
            'expires_in' => $access_token->getExpires() - time(),
            'scope' => implode(' ', $scope),
            'state' => $state,
        );

        if ($withRefreshToken === true) {
            $modelManager = $modelManagerFactory->getModelManager('refresh_token');
            $refresh_token = $modelManager->createModel();
            $refresh_token->setRefreshToken(md5(uniqid(null, true)))
                ->setClientId($client_id)
                ->setUsername($username)
                ->setExpires(time() + 86400)
                ->setScope($scope);
            $modelManager->updateModel($refresh_token);

            $parameters['refresh_token'] = $refresh_token->getRefreshToken();
        }

        return $parameters;
    }
}
