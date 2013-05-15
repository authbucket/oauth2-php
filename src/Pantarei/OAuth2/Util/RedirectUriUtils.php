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

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Util\ClientIdUtils;

/**
 * Redirect URI related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class RedirectUriUtils
{
  /**
   * Override redirect_uri with stored recored, if exists in database.
   *
   * @param array $query
   *   The original query.
   *
   * @param string
   *   The original query with redirect_uri fetched.
   */
  public static function fetch($query) {
    // redirect_uri is not required if already established via other channels,
    // check an existing redirect URI against the one supplied.
    $result = Database::findOneBy('Clients', array(
      'client_id' => $query['client_id'],
    ));
    if ($result !== NULL && $result->getRedirectUri()) {
      $query['redirect_uri'] = $result->getRedirectUri();
    }
    return $query;
  }

  /**
   * Check if redirect_uri valid.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query which processed by ParamUtils::filter().
   *
   * @return boolean
   *   TRUE if valid, or else FALSE.
   *
   * @throws \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public static function check($query, $filtered_query) {
    // At least one of: existing redirect URI or input redirect URI must be
    // specified.
    if (!isset($filtered_query['redirect_uri']) && !isset($query['redirect_uri'])) {
      throw new InvalidRequestException();
    }

    // If there's an existing uri and one from input, verify that they match.
    if (isset($filtered_query['redirect_uri']) && isset($query['redirect_uri'])) {
      // Ensure that the input uri starts with the stored uri.
      if (strcasecmp(substr($filtered_query["redirect_uri"], 0, strlen($query['redirect_uri'])), $query['redirect_uri']) !== 0) {
        throw new InvalidRequestException();
      }
    }

    return TRUE;
  }
}
