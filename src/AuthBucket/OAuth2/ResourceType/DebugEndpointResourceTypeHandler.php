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

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Token response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugEndpointResourceTypeHandler extends AbstractResourceTypeHandler
{
    public function handle(
        HttpKernelInterface $httpKernel,
        ModelManagerFactoryInterface $modelManagerFactory,
        $accessToken,
        array $options = array()
    )
    {
        $options = array_merge(array(
            'token_path' => '',
            'debug_path' => '',
            'client_id' => '',
            'client_secret' => '',
            'cache' => false,
        ), $options);

        // Both options are required.
        if (!$options['token_path']
            || !$options['debug_path']
            || !$options['client_id']
            || !$options['client_secret']
        ) {
            throw new ServerErrorException();
        }

        $accessTokenManager = $modelManagerFactory->getModelManager('access_token');

        // Get cached access_token and return if exists.
        if ($options['cache']) {
            $stored = $accessTokenManager->findAccessTokenByAccessToken($accessToken);
            if ($stored !== null && $stored->getExpires() > new \DateTime()) {
                return $stored;
            }
        }

        // Get client credentials grant-ed access token for resource server.
        $parameters = array(
            'grant_type' => 'client_credentials',
            'scope' => 'debug',
        );
        $server = array(
            'PHP_AUTH_USER' => $options['client_id'],
            'PHP_AUTH_PW' => $options['client_secret'],
        );
        $client = new Client($httpKernel);
        $crawler = $client->request('POST', $options['token_path'], $parameters, array(), $server);
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);

        // If error throw original authorize server response.
        if (isset($tokenResponse['error'])) {
            throw new \Exception(
                serialize($client->getResponse()->getContent()),
                $client->getResponse()->getStatusCode()
            );
        }

        // Fetch meta data of supplied access token by query debug endpoint.
        $parameters = array(
            'debug_token' => $accessToken,
        );
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = new Client($httpKernel);
        $crawler = $client->request('GET', $options['debug_path'], $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);

        // If error throw original authorize server response.
        if (isset($debugResponse['error'])) {
            throw new \Exception(
                serialize($client->getResponse()->getContent()),
                $client->getResponse()->getStatusCode()
            );
        }

        // Create a new access token with fetched meta data.
        $stored = $accessTokenManager->createAccessToken();
        $stored->setAccessToken($debugResponse['access_token'])
            ->setTokenType($debugResponse['token_type'])
            ->setClientId($debugResponse['client_id'])
            ->setUsername($debugResponse['username'])
            ->setExpires($debugResponse['expires'])
            ->setScope($debugResponse['scope']);

        // Set fetched access token into cache.
        if ($options['cache']) {
            $accessTokenManager->updateAccessToken($stored);
        }

        return $stored;
    }
}
