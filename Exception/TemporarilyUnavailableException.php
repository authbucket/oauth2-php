<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Exception;

/**
 * TemporarilyUnavailableException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TemporarilyUnavailableException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.2.1
   */
  protected $message =
    'The authorization server is currently unable to handle ' .
    'the request due to a temporary overloading or maintenance ' .
    'of the server.  (This error code is needed because a 503 ' .
    'Service Unavailable HTTP status code cannot be returned ' .
    'to the client via an HTTP redirect.)';
}
