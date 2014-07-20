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

use AuthBucket\OAuth2\Exception\InvalidClientException;
use AuthBucket\OAuth2\Model\ClientInterface;
use AuthBucket\OAuth2\Model\ModelManagerFactoryInterface;
use AuthBucket\OAuth2\Security\Authentication\Token\ClientToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * TokenProvider implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenProvider implements AuthenticationProviderInterface
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

        $clientId = $token->getClientId();
        $clientSecret = $token->getClientSecret();

        $clientManager = $this->modelManagerFactory->getModelManager('client');
        $clientStored = $clientManager->findClientByClientId($clientId);
        if ($clientStored === null) {
            throw new InvalidClientException(array(
                'error_description' => 'Client authentication failed.',
            ));
        }
        $clientSupplied = $token->getClient();

        if ($clientSupplied instanceof ClientInterface) {
            if ($clientStored->getClientSecret() !== $clientSupplied->getClientSecret()) {
                throw new InvalidClientException(array(
                    'error_description' => 'Client authentication failed.',
                ));
            }
        } else {
            if ($clientStored->getClientSecret() !== $clientSecret) {
                throw new InvalidClientException(array(
                    'error_description' => 'Client authentication failed.',
                ));
            }
        }

        $tokenAuthenticated = new ClientToken($clientId, $clientSecret, $this->providerKey, $token->getRoles());
        $tokenAuthenticated->setClient($clientStored);
        $tokenAuthenticated->setUser($clientStored->getClientId());

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ClientToken && $this->providerKey === $token->getProviderKey();
    }
}
