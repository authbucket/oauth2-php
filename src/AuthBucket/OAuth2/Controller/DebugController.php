<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Controller;

use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Util\Filter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 debug endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugController
{
    protected $modelManagerFactory;

    public function __construct(
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function debugAction(Request $request)
    {
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');

        // Fetch access_token from GET.
        $debug = $this->getDebug($request);
        $access_token = $accessTokenManager->findAccessTokenByAccessToken($debug);

        // Handle debug endpoint response.
        $parameters = array(
            'access_token' => $access_token->getAccessToken(),
            'token_type' => $access_token->getTokenType(),
            'client_id' => $access_token->getClientId(),
            'username' => $access_token->getUsername(),
            'expires' => $access_token->getExpires()->getTimestamp(),
            'scope' => $access_token->getScope(),
        );

        return JsonResponse::create($parameters);
    }

    private function getDebug(Request $request)
    {
        // Validate and set access_token.
        $debug = $request->query->get('debug')
            ?: $request->request->get('debug');
        $query = array(
            'access_token' => $debug
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $debug;
    }
}
