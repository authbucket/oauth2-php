<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2;

/**
 * Base OAuth2 type interface for response, grant and token type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface OAuth2TypeInterface
{
  public function setUsername($username);

  public function getUsername();

  public function setClientId($client_id);

  public function getClientId();

  public function setScope($scope);

  public function getScope();
}
