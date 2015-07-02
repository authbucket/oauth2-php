<?php

/**
 * This file is part of the authbucket/oauth2-php package.
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
    ) {
        $options = array_merge(array(
            'debug_endpoint' => '',
            'cache' => true,
        ), $options);

        // Both options are required.
        if (!$options['debug_endpoint']) {
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
        if (strpos($options['debug_endpoint'], '/') === 0) {
            // For relative URL, use Symfony test client to simulates and
            // HTTP client like a browser and makes requests.
            $client = new \Symfony\Component\HttpKernel\Client($this->httpKernel);
            $crawler = $client->request('GET', $options['debug_endpoint'], array(), array(), array(
                'HTTP_Authorization' => implode(' ', array('Bearer', $accessToken)),
            ));
            $content = $client->getResponse()->getContent();
        } else {
            // For absolute URL, use Guzzle client to create request.
            $client = new \Guzzle\Http\Client();
            $crawler = $client->get($options['debug_endpoint'], array(), array(
                'headers' => array('Authorization' => implode(' ', array('Bearer', $accessToken))),
                'exceptions' => false,
            ));
            $content = $crawler->send()->getBody();
        }
        $response = json_decode($content, true);

        // Throw exception if error return.
        if (isset($response['error'])) {
            throw new InvalidRequestException(array(
                'error_description' => 'The request includes an invalid parameter value.',
            ));
        }

        // Create a new access token with fetched meta data.
        $class = $accessTokenManager->getClassName();
        $accessTokenCached = new $class();
        $accessTokenCached->setAccessToken($response['access_token'])
            ->setTokenType($response['token_type'])
            ->setClientId($response['client_id'])
            ->setUsername($response['username'])
            ->setExpires(new \DateTime('@'.$response['expires']))
            ->setScope($response['scope']);
        $accessTokenCached = $accessTokenManager->createModel($accessTokenCached);

        return $accessTokenCached;
    }
}
