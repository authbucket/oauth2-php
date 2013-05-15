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
 * Code related utility for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class CodeUtils
{
  /**
   * Check if code valid.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query which processed by ParamUtils::filter().
   *
   * @return boolean
   *   TRUE if valid, or else FALSE.
   *
   * @throws \Pantarei\OAuth2\Exception\InvalidGrantException
   * @throws \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public static function check(Application $app, $query, $filtered_query) {
    // code is required and must in good format.
    if (!isset($filtered_query['code'])) {
      throw new InvalidRequestException();
    }

    // If refresh_token is invalid we should stop here.
    $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Codes')->findOneBy(array(
      'code' => $filtered_query['code'],
    ));
    if ($result == NULL) {
      throw new InvalidGrantException();
    }
    elseif ($result->getExpires() < time()) {
      throw new InvalidRequestException();
    }

    return TRUE;
  }
}
