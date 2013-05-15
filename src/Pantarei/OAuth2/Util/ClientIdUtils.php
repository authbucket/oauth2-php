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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Silex\Application;

/**
 * Client ID related utility for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ClientIdUtils
{
  /**
   * Check if client_id provided valid.
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
   * @throws \Pantarei\OAuth2\Exception\UnauthorizedClientException
   */
  public static function check(Application $app, $query, $filtered_query) {
    // client_id is required and must in good format.
    if (!isset($filtered_query['client_id']) && !isset($query['client_id'])) {
      throw new InvalidRequestException();
    }

    // If client_id is invalid we should stop here.
    $client = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
      'client_id' => $query['client_id'],
    ));
    if ($client == NULL) {
      throw new UnauthorizedClientException();
    }

    return TRUE;
  }
}
