<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Util;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Silex\Application;

/**
 * Resource owner credentials related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ResourceOwnerCredentialUtils
{
  /**
   * Check if resource owner credentials valid.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query.
   *
   * @return boolean
   *   TRUE if valid, or else FALSE.
   *
   * @throws \Pantarei\OAuth2\Exception\InvalidGrantException
   * @throws \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public static function check(Application $app, $query, $filtered_query)
  {
    // username and password are required.
    if (!isset($filtered_query['username']) || !isset($filtered_query['password'])) {
      throw new InvalidRequestException();
    }

    // If username and password invalid we should stop here.
    $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Users')->findOneBy(array(
      'username' => $filtered_query['username'],
      'password' => $filtered_query['password'],
    ));
    if ($result == NULL) {
      throw new InvalidGrantException();
    }

    return TRUE;
  }
}
