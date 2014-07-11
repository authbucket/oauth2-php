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
use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Model\AccessTokenInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * ResourceProvider implements OAuth2 resource endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceProvider implements AuthenticationProviderInterface
{
    protected $modelManagerFactory;
    protected $providerKey;
    protected $resourceType;
    protected $scopeRequired;
    protected $options;

    public function __construct(
        ModelManagerFactoryInterface $modelManagerFactory,
        $providerKey,
        $resourceType = 'model',
        array $scopeRequired = array(),
        array $options = array()
    )
    {
        $this->modelManagerFactory = $modelManagerFactory;
        $this->providerKey = $providerKey;
        $this->resourceType = $resourceType;
        $this->scopeRequired = $scopeRequired;
        $this->options = $options;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $accessTokenSupplied = '';
        $scopeSupplied = array();

        $tokenSupplied = $token->getAccessToken();
        if ($tokenSupplied instanceof AccessTokenInterface) {
            $accessTokenSupplied = $accessToken->getAccessToken();
            $scopeSupplied = $accessToken->getScope();
        } else {
            $accessTokenSupplied = $tokenSupplied;
        }

        $accessTokenManager = $this->modelManagerFactory->getModelManager('access_token');
        $accessTokenStored = $accessTokenManager->findAccessTokenByAccessToken($accessTokenSupplied);
        if ($accessTokenStored === null) {
            throw new AccessDeniedException();
        } elseif ($accessTokenStored->getExpires() < new \DateTime()) {
            throw new AccessDeniedException();
        }

        if ($this->scopeRequired) {
            if (array_intersect($this->scopeRequired, $scopeSupplied) != $this->scopeRequired) {
                throw new InvalidScopeException();
            }
        }

        $tokenAuthenticated = new AccessToken($accessTokenStored, $this->providerKey);
        $tokenAuthenticated->setUser($accessTokenStored->getUsername());

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof AccessToken;
    }
}
