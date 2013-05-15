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
use Silex\Application;

/**
 * Scope related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ScopeUtils
{
  /**
   * Check if scope provided valid.
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
  public static function check(Application $app, $query, $filtered_query) {
    // scope is optional.
    if (isset($query['scope'])) {
      if (!isset($filtered_query['scope'])) {
        throw new InvalidScopeException();
      }

      // Check scope from database.
      foreach (preg_split("/\s+/", $filtered_query['scope']) as $scope) {
        $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Scopes')->findOneBy(array(
          'scope' => $scope,
        ));
        if ($result == NULL) {
          throw new InvalidScopeException();
        }
      }
      return TRUE;
    }
    return FALSE;
  }
}
