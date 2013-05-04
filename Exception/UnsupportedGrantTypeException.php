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
 * UnsupportedGrantTypeException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UnsupportedGrantTypeException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-5.2
   */
  protected $message =
    'The authorization grant type is not supported by the ' .
    'authorization server.';
}
