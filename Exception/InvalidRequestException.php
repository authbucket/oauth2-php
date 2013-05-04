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
 * InvalidRequestException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class InvalidRequestException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-5.2
   */
  protected $message =
    'The request is missing a required parameter, includes an ' .
    'unsupported parameter value (other than grant type), ' .
    'repeats a parameter, includes multiple credentials, ' .
    'utilizes more than one mechanism for authenticating the ' .
    'client, or is otherwise malformed.';
}
