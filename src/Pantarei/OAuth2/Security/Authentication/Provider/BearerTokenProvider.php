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

use Pantarei\OAuth2\Exception\AccessDeniedException;
use Pantarei\OAuth2\Model\AccessTokenInterface;
use Pantarei\OAuth2\Model\AccessTokenManagerInterface;
use Pantarei\OAuth2\Security\Authentication\Token\BearerToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * BearerTokenProvider implements OAuth2 token endpoint authentication.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenProvider implements AuthenticationProviderInterface
{
    private $accessTokenManager;

    public function __construct(
        AccessTokenManagerInterface $accessTokenManager
    )
    {
        $this->accessTokenManager = $accessTokenManager;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $access_token = $token->getAccessToken();

        $user = $this->accessTokenManager->findAccessTokenByAccessToken($access_token);
        if ($user === null) {
            throw new AccessDeniedException();
        }

        $authenticatedToken = new BearerToken($access_token, $token->getRoles());
        $authenticatedToken->setAccessToken($user);

        return $authenticatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof BearerToken;
    }
}
