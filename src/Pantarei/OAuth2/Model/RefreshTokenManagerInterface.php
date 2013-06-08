<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Model;

interface RefreshTokenManagerInterface
{
    public function createRefreshToken();

    public function deleteRefreshToken(RefreshTokenInterface $refresh_token);

    public function findRefreshTokenByRefreshToken($refresh_token);

    public function getClassName();

    public function reloadRefreshToken(RefreshTokenInterface $refresh_token);

    public function updateRefreshToken(RefreshTokenInterface $refresh_token);
}
