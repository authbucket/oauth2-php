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
    ) {
        $this->providerKey = $providerKey;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }

        $clientManager = $this->modelManagerFactory->getModelManager('client');
        $client = $clientManager->readModelOneBy(array(
            'clientId' => $token->getClientId(),
        ));
        if ($client === null || $client->getClientSecret() !== $token->getClientSecret()) {
            throw new InvalidClientException(array(
                'error_description' => 'Client authentication failed.',
            ));
        }

        $tokenAuthenticated = new ClientToken(
            $this->providerKey,
            $client->getClientId(),
            $client->getClientSecret(),
            $client->getRedirectUri(),
            $token->getRoles()
        );
        $tokenAuthenticated->setUser($client->getClientId());

        return $tokenAuthenticated;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ClientToken && $this->providerKey === $token->getProviderKey();
    }
}
