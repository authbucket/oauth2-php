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
 * InvalidGrantException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class InvalidGrantException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-5.2
   */
  protected $message =
    'The provided authorization grant (e.g., authorization ' .
    'code, resource owner credentials) or refresh token is ' .
    'invalid, expired, revoked, does not match the redirection ' .
    'URI used in the authorization request, or was issued to ' .
    'another client.';
}
