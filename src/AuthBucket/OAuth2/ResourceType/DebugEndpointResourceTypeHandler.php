<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\ResourceType;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

/**
 * Token response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugEndpointResourceTypeHandler extends AbstractResourceTypeHandler
{
    public function handle(
        $accessToken,
        array $options = array()
    )
    {
        $options = array_merge(array(
            'token_path' => '/oauth2/token',
            'debug_path' => '/oauth2/debug',
            'cache' => true,
        ), $options);

        // Both options are required.
        if (!$options['token_path']
            || !$options['debug_path']
        ) {
            throw new ServerErrorException(array(
                'error_description' => 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.',
            ));
        }

        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');

        // Get cached access_token and return if exists.
        if ($options['cache']) {
            $stored = $accessTokenManager->readModelOneBy(array(
                'accessToken' => $accessToken,
            ));
            if ($stored !== null && $stored->getExpires() > new \DateTime()) {
                return $stored;
            }
        }

        // Fetch meta data of supplied access token by query debug endpoint.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $accessToken)),
        );
        $client = new Client($this->httpKernel);
        $crawler = $client->request('GET', $options['debug_path'], $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);

        // Throw exception if error return.
        if (isset($debugResponse['error'])) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Create a new access token with fetched meta data.
        $class = $accessTokenManager->getClassName();
        $accessTokenCached = new $class();
        $accessTokenCached->setAccessToken($debugResponse['access_token'])
            ->setTokenType($debugResponse['token_type'])
            ->setClientId($debugResponse['client_id'])
            ->setUsername($debugResponse['username'])
            ->setExpires(new \DateTime('@' . $debugResponse['expires']))
            ->setScope($debugResponse['scope']);
        $accessTokenCached = $accessTokenManager->createModel($accessTokenCached);

        return $accessTokenCached;
    }
}
