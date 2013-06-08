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

interface AuthorizeManagerInterface
{
    public function createAuthorize();

    public function deleteAuthorize(AuthorizeInterface $authorize);

    public function findAuthorizeByClientIdUsername($client_id, $username);

    public function getClassName();

    public function reloadAuthorize(AuthorizeInterface $authorize);

    public function updateAuthorize(AuthorizeInterface $authorize);
}
