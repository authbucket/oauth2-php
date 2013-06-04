<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Security\Authentication\Provider;

use Pantarei\OAuth2\Security\Authentication\Token\ClientToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * TokenProvider implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $encoderFactory;

    public function __construct(
        UserProviderInterface $userProvider,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $this->userProvider = $userProvider;
        $this->encoderFactory = $encoderFactory;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $client_id = $token->getUsername();
        $client_secret = $token->getCredentials();

        $client = $this->userProvider->loadUserByUsername($client_id);
        $currentClient = $token->getUser();

        if ($currentClient instanceof UserInterface) {
            if ($currentClient->getPassword() !== $client->getPassword()) {
                throw new BadCredentialsException('The credentials were changed from another session.');
            }
        } else {
            $encoder = $this->encoderFactory->getEncoder($client);
            if (!$encoder->isPasswordValid($client->getPassword(), $client_secret, $client->getSalt())) {
                throw new BadCredentialsException('The presented password is invalid.');
            }
        }

        $authenticatedToken = new ClientToken($client_id, $client_secret, $token->getRoles());
        $authenticatedToken->setUser($client);

        return $authenticatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ClientToken;
    }
}
