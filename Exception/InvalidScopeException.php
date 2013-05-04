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
 * InvalidScopeException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class InvalidScopeException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-5.2
   */
  protected $message =
    'The requested scope is invalid, unknown, malformed, or ' .
    'exceeds the scope granted by the resource owner.';
}
