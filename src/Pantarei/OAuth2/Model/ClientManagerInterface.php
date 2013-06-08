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

interface ClientManagerInterface
{
    public function createClient();

    public function deleteClient(ClientInterface $client);

    public function findClientByClientId($client_id);

    public function getClassName();

    public function reloadClient(ClientInterface $client);

    public function updateClient(ClientInterface $client);
}
