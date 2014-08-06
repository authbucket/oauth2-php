<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\TokenType;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bearer token type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenTypeHandler implements TokenTypeHandlerInterface
{
    public function getAccessToken(Request $request)
    {
        $tokenHeaders = $request->headers->get('Authorization', false);
        if ($tokenHeaders && preg_match('/Bearer\s*([^\s]+)/', $tokenHeaders, $matches)) {
            $tokenHeaders = $matches[1];
        } else {
            $tokenHeaders = false;
        }
        $tokenRequest = $request->request->get('access_token', false);
        $tokenQuery = $request->query->get('access_token', false);

        // At least one (and only one) of client credentials method required.
        if (!$tokenHeaders && !$tokenRequest && !$tokenQuery) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request is missing a required parameter.',
            ));
        } elseif (($tokenHeaders && $tokenRequest)
            || ($tokenRequest && $tokenQuery)
            || ($tokenQuery && $tokenHeaders)
        ) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes multiple credentials.',
            ));
        }

        // Check with HTTP basic auth if exists.
        $accessToken = $tokenHeaders
            ?: $tokenRequest
            ?: $tokenQuery;

        return $accessToken;
    }

    public function createAccessToken(
        ModelManagerFactoryInterface $modelManagerFactory,
        $clientId,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    )
    {
        $accessTokenManager = $modelManagerFactory->getModelManager('access_token');
        $accessToken = $accessTokenManager->createModel(array(
            'accessToken' => md5(uniqid(null, true)),
            'tokenType' => 'bearer',
            'clientId' => $clientId,
            'username' => $username,
            'expires' => new \DateTime('+1 hours'),
            'scope' => (array) $scope,
        ));

        $parameters = array(
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => $accessToken->getTokenType(),
            'expires_in' => $accessToken->getExpires()->getTimestamp() - time(),
        );

        if (!empty($scope)) {
            $parameters['scope'] = implode(' ', (array) $scope);
        }

        if (!empty($state)) {
            $parameters['state'] = $state;
        }

        if ($withRefreshToken === true) {
            $refreshTokenManager = $modelManagerFactory->getModelManager('refresh_token');
            $refreshToken = $refreshTokenManager->createModel(array(
                'refreshToken' => md5(uniqid(null, true)),
                'clientId' => $clientId,
                'username' => $username,
                'expires' => new \DateTime('+1 days'),
                'scope' => (array) $scope,
            ));

            $parameters['refresh_token'] = $refreshToken->getRefreshToken();
        }

        return $parameters;
    }
}
