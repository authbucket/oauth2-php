<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

interface GrantTypeHandlerFactoryInterface
{
    public function addGrantTypeHandler($type, $handler);

    public function getGrantTypeHandler($type);

    public function removeGrantTypeHandler($type);
}
