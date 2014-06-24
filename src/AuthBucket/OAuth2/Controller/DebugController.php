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
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * OAuth2 debug endpoint controller implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DebugController
{
    protected $securityContext;
    protected $modelManagerFactory;

    public function __construct(
        SecurityContextInterface $securityContext,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->securityContext = $securityContext;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function debugAction(Request $request)
    {
        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');

        // Fetch access_token from GET.
        $debug = $this->getDebug($request);
        $access_token = $accessTokenManager->findAccessTokenByAccessToken($debug);
        if (null === $access_token) {
            throw new InvalidRequestException();
        } elseif ($access_token->getExpires() < new \DateTime()) {
            throw new InvalidRequestException();
        }

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
        // Fetch debug token from GET/POST/access_token.
        $debug = $request->query->get('debug')
            ?: $request->request->get('debug')
            ?: $this->securityContext->getToken()->getAccessToken()->getAccessToken();
        if (null === $debug) {
            throw new InvalidRequestException();
        }

        // Validate debug token.
        $query = array(
            'access_token' => $debug,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $debug;
    }
}
