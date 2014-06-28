<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Authentication\Provider;

use AuthBucket\OAuth2\Exception\AccessDeniedException;
use AuthBucket\OAuth2\Model\AccessTokenInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * TokenProvider implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceProvider implements AuthenticationProviderInterface
{
    protected $modelManagerFactory;
    protected $providerKey;

    public function __construct(
        ModelManagerFactoryInterface $modelManagerFactory,
        $providerKey
    )
    {
        $this->modelManagerFactory = $modelManagerFactory;
        $this->providerKey = $providerKey;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $access_token = $token->getAccessToken();
        if ($access_token instanceof AccessTokenInterface) {
            $access_token = $access_token->getAccessToken();
        }

        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $storedAccessToken = $accessTokenManager->findAccessTokenByAccessToken($access_token);
        if ($storedAccessToken === null) {
            throw new AccessDeniedException();
        } elseif ($storedAccessToken->getExpires() < new \DateTime()) {
            throw new AccessDeniedException();
        }

        $authenticatedToken = new AccessToken($storedAccessToken, $this->providerKey);
        $authenticatedToken->setUser($storedAccessToken->getUsername());

        return $authenticatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof AccessToken;
    }
}
