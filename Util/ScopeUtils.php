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

use Pantarei\OAuth2\Exception\InvalidScopeException;

/**
 * Scope related utility for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ScopeUtils
{
  /**
   * Check if scope provided valid.
   *
   * @todo Check supported scope from database.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query which processed by ParamUtils::filter().
   *
   * @return boolean
   *   TRUE if valid, or else FALSE.
   *
   * @throws \Pantarei\OAuth2\Exception\InvalidScopeException
   */
  public static function check($query, $filtered_query) {
    if (isset($query['scope'])) {
      if (!isset($filtered_query['scope'])) {
        throw new InvalidScopeException();
      }
      return TRUE;
    }
    return FALSE;
  }
}
