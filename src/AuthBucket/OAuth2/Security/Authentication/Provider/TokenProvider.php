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
    protected $providerKey;
    protected $modelManagerFactory;

    public function __construct(
        $providerKey,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->providerKey = $providerKey;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $clientId = $token->getClientId();
        $clientSecret = $token->getClientSecret();

        $clientManager = $this->modelManagerFactory->getModelManager('client');
        $client = $clientManager->readModelOneBy(array(
            'clientId' => $clientId,
        ));
        if ($client === null || $client->getClientSecret() !== $clientSecret) {
            throw new InvalidClientException(array(
                'error_description' => 'Client authentication failed.',
            ));
        }

        $tokenAuthenticated = new ClientToken(
            $client->getClientId(),
            $client->getClientSecret(),
            $client->getRedirectUri(),
            $this->providerKey,
            $token->getRoles()
        );
        $tokenAuthenticated->setUser($clientId);

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ClientToken && $this->providerKey === $token->getProviderKey();
    }
}
