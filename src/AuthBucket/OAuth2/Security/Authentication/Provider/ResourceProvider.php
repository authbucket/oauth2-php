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

use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\Model\AccessTokenInterface;
use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactoryInterface;
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
    protected $resourceTypeHandlerFactory;
    protected $providerKey;
    protected $resourceType;
    protected $scopeRequired;
    protected $options;

    public function __construct(
        ResourceTypeHandlerFactoryInterface $resourceTypeHandlerFactory,
        $providerKey,
        $resourceType = 'model',
        array $scopeRequired = array(),
        array $options = array()
    )
    {
        $this->resourceTypeHandlerFactory = $resourceTypeHandlerFactory;
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

        $tokenSupplied = $token->getAccessToken();

        // Handle different resource type access_token check.
        $accessTokenSupplied = $tokenSupplied instanceof AccessTokenInterface
            ? $accessToken->getAccessToken()
            : $tokenSupplied;
        $accessTokenStored = $this->resourceTypeHandlerFactory
            ->getResourceTypeHandler($this->resourceType)
            ->handle(
                $accessTokenSupplied,
                $this->options
            );

        // Check if enough scope supplied.
        $scopeSupplied = $accessTokenStored->getScope() ?: array();
        if ($this->scopeRequired) {
            if (array_intersect($this->scopeRequired, $scopeSupplied) != $this->scopeRequired) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope is malformed.',
                ));
            }
        }

        $tokenAuthenticated = new AccessToken($accessTokenStored, $this->providerKey);
        $tokenAuthenticated->setUser($accessTokenStored->getUsername());

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof AccessToken && $this->providerKey === $token->getProviderKey();
    }
}
