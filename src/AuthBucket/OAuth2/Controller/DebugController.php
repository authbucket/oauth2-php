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
        $debugToken = $this->getDebugToken($request);
        $accessToken = $accessTokenManager->findAccessTokenByAccessToken($debugToken);
        if (null === $accessToken) {
            throw new InvalidRequestException();
        } elseif ($accessToken->getExpires() < new \DateTime()) {
            throw new InvalidRequestException();
        }

        // Handle debug endpoint response.
        $parameters = array(
            'access_token' => $accessToken->getAccessToken(),
            'token_type' => $accessToken->getTokenType(),
            'client_id' => $accessToken->getClientId(),
            'username' => $accessToken->getUsername(),
            'expires' => $accessToken->getExpires()->getTimestamp(),
            'scope' => $accessToken->getScope(),
        );

        return JsonResponse::create($parameters);
    }

    private function getDebugToken(Request $request)
    {
        // Fetch debug token from GET/POST/access_token.
        $debugToken = $request->query->get('debug_token')
            ?: $request->request->get('debug_token')
            ?: $this->securityContext->getToken()->getAccessToken()->getAccessToken();

        // Validate debug token.
        $query = array(
            'access_token' => $debugToken,
        );
        if (!Filter::filter($query)) {
            throw new InvalidRequestException();
        }

        return $debugToken;
    }
}
