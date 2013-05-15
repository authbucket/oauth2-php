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

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Silex\Application;

/**
 * Client credentials related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ClientCredentialUtils
{
  /**
   * Override client_id with HTTP basic auth data.
   *
   * @param array $query
   *   The original query.
   *
   * @param string
   *   The original query with redirect_uri fetched.
   */
  public static function fetch(Application $app, $query) {
    if (isset($_SERVER['PHP_AUTH_USER'])) {
      $query['client_id'] = $_SERVER['PHP_AUTH_USER'];
      $query['client_secret'] = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
    }
    return $query;
  }

  /**
   * Check if client credentials valid from HTTP basid auth or POST data.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query.
   *
   * @return boolean
   *   TRUE if valid, or else FALSE.
   *
   * @throws \Pantarei\OAuth2\Exception\InvalidClientException
   * @throws \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public static function check(Application $app, $query, $filtered_query)
  {
    // At least one (and only one) of client credentials method required.
    if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($filtered_query['client_id'])) {
      throw new InvalidClientException();
    }
    elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($filtered_query['client_id'])) {
      throw new InvalidRequestException();
    }

    // Try HTTP basic auth.
    if (isset($_SERVER['PHP_AUTH_USER'])) {
      $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $_SERVER['PHP_AUTH_USER'],
        'client_secret' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
      ));
      if ($result == NULL) {
        return FALSE;
      }
    }
    // Try POST
    elseif (isset($query['client_id'])) {
      $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $query['client_id'],
        'client_secret' => isset($query['client_secret']) ? $query['client_secret'] : '',
      ));
      if ($result == NULL) {
        return FALSE;
      }
    }

    return TRUE;
  }
}
