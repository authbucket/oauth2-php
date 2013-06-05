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

use Pantarei\OAuth2\Security\Authentication\Token\BearerToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * BearerTokenProvider implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenProvider implements AuthenticationProviderInterface
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

        $access_token = $token->getCredentials();

        $user = $this->userProvider->loadUserByAccessToken($access_token);

        $authenticatedToken = new BearerToken($access_token, $token->getRoles());
        $authenticatedToken->setUser($user);

        return $authenticatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof BearerToken;
    }
}
