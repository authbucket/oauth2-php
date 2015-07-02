<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Security\Authentication\Provider;

use AuthBucket\OAuth2\Exception\InvalidScopeException;
use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\AccessTokenToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * ResourceProvider implements OAuth2 resource endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceProvider implements AuthenticationProviderInterface
{
    protected $providerKey;
    protected $resourceTypeHandlerFactory;
    protected $resourceType;
    protected $scopeRequired;
    protected $options;

    public function __construct(
        $providerKey,
        ResourceTypeHandlerFactoryInterface $resourceTypeHandlerFactory,
        $resourceType = 'model',
        array $scopeRequired = array(),
        array $options = array()
    ) {
        $this->providerKey = $providerKey;
        $this->resourceTypeHandlerFactory = $resourceTypeHandlerFactory;
        $this->resourceType = $resourceType;
        $this->scopeRequired = $scopeRequired;
        $this->options = $options;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }

        // Handle different resource type access_token check.
        $accessToken = $this->resourceTypeHandlerFactory
            ->getResourceTypeHandler($this->resourceType)
            ->handle(
                $token->getAccessToken(),
                $this->options
            );

        // Check if enough scope supplied.
        $scope = $accessToken->getScope() ?: array();
        if ($this->scopeRequired) {
            if (array_intersect($this->scopeRequired, $scope) != $this->scopeRequired) {
                throw new InvalidScopeException(array(
                    'error_description' => 'The requested scope is malformed.',
                ));
            }
        }

        $tokenAuthenticated = new AccessTokenToken(
            $this->providerKey,
            $accessToken->getAccessToken(),
            $accessToken->getTokenType(),
            $accessToken->getClientId(),
            $accessToken->getUsername(),
            $accessToken->getExpires(),
            $accessToken->getScope(),
            $token->getRoles()
        );
        $tokenAuthenticated->setUser($accessToken->getUsername());

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof AccessTokenToken && $this->providerKey === $token->getProviderKey();
    }
}
