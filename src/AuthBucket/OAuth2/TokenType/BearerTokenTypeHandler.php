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
            throw new InvalidRequestException();
        } elseif (($tokenHeaders && $tokenRequest)
            || ($tokenRequest && $tokenQuery)
            || ($tokenQuery && $tokenHeaders)
        ) {
            throw new InvalidRequestException();
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
        $modelManager = $modelManagerFactory->getModelManager('access_token');
        $accessToken = $modelManager->createAccessToken()
            ->setAccessToken(md5(uniqid(null, true)))
            ->setTokenType('bearer')
            ->setClientId($clientId)
            ->setUsername($username)
            ->setExpires(new \DateTime('+1 hours'))
            ->setScope($scope);
        $modelManager->updateAccessToken($accessToken);

        $parameters = array(
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => $accessToken->getTokenType(),
            'expires_in' => $accessToken->getExpires()->getTimestamp() - time(),
        );

        if (!empty($scope) && is_array($scope)) {
            $parameters['scope'] = implode(' ', $scope);
        }

        if (!empty($state)) {
            $parameters['state'] = $state;
        }

        if ($withRefreshToken === true) {
            $modelManager = $modelManagerFactory->getModelManager('refresh_token');
            $refreshToken = $modelManager->createRefreshToken()
                ->setRefreshToken(md5(uniqid(null, true)))
                ->setClientId($clientId)
                ->setUsername($username)
                ->setExpires(new \DateTime('+1 days'))
                ->setScope($scope);
            $modelManager->updateRefreshToken($refreshToken);

            $parameters['refresh_token'] = $refreshToken->getRefreshToken();
        }

        return $parameters;
    }
}
