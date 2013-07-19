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

interface AuthorizeManagerInterface extends ModelManagerInterface
{
    public function createAuthorize(
        $client_id,
        $username,
        $scope = array()
    );

    public function deleteAuthorize(AuthorizeInterface $authorize);

    public function reloadAuthorize(AuthorizeInterface $authorize);

    public function updateAuthorize(AuthorizeInterface $authorize);

    public function findAuthorizeByClientIdAndUsername($client_id, $username);
}
