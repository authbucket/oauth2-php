<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Request;

/**
 * Access token request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    if (!isset($query['client_id'])) {
      throw new Exception\InvalidClientException('No client id supplied', 0);
    }

    return TRUE;
  }

}
