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

use Silex\Application;

/**
 * Defines the interface for request.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface Request
{
  /**
   * Validate the request to ensure that all required parameters are present
   * and valid.
   *
   * @return object
   *   The object of ResponseType or GrantType.
   */
  public function validateRequest(Application $app);
}
