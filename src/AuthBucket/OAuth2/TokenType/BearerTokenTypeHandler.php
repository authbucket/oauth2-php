<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\TokenType;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Validator\Constraints\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Bearer token type handler implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenTypeHandler extends AbstractTokenTypeHandler
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

        // access_token must be in valid format.
        $errors = $this->validator->validateValue($accessToken, array(
            new NotBlank(),
            new AccessToken(),
        ));
        if (count($errors) > 0) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        return $accessToken;
    }

    public function createAccessToken(
        $clientId,
        $username = '',
        $scope = array(),
        $state = null,
        $withRefreshToken = true
    ) {
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $class = $accessTokenManager->getClassName();
        $accessToken = new $class();
        $accessToken->setAccessToken(md5(uniqid(null, true)))
            ->setTokenType('bearer')
            ->setClientId($clientId)
            ->setUsername($username)
            ->setExpires(new \DateTime('+1 hours'))
            ->setScope((array) $scope);
        $accessToken = $accessTokenManager->createModel($accessToken);

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
            $refreshTokenManager = $this->modelManagerFactory->getModelManager('refresh_token');
            $class = $refreshTokenManager->getClassName();
            $refreshToken = new $class();
            $refreshToken->setRefreshToken(md5(uniqid(null, true)))
                ->setClientId($clientId)
                ->setUsername($username)
                ->setExpires(new \DateTime('+1 days'))
                ->setScope((array) $scope);
            $refreshToken = $refreshTokenManager->createModel($refreshToken);

            $parameters['refresh_token'] = $refreshToken->getRefreshToken();
        }

        return $parameters;
    }
}
