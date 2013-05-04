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
 * InvalidClientException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class InvalidClientException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-5.2
   */
  protected $message =
    'Client authentication failed (e.g., unknown client, no ' .
    'client authentication included, or unsupported ' .
    'authentication method).  The authorization server MAY ' .
    'return an HTTP 401 (Unauthorized) status code to indicate ' .
    'which HTTP authentication schemes are supported.  If the ' .
    'client attempted to authenticate via the "Authorization" ' .
    'request header field, the authorization server MUST ' .
    'respond with an HTTP 401 (Unauthorized) status code and ' .
    'include the "WWW-Authenticate" response header field ' .
    'matching the authentication scheme used by the client.';
}
