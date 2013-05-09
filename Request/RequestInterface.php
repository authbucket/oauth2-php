<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Request;

/**
 * Defines the interface for request.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface RequestInterface
{
  /**
   * Validate the request to ensure that all required parameters are present
   * and valid.
   *
   * @param array $query
   *   The query component, most likely $_GET.
   *
   * @return array|false
   *   The filtered request query parameters if valid, or FALSE otherwise.
   */
  public function validateRequest(array $query = array());
}
