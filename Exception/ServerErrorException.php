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
 * ServerErrorException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ServerErrorException extends \Exception
{
  /**
   * Error Response
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.2.1
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.2.1
   */
  protected $message =
    'The authorization server encountered an unexpected ' .
    'condition that prevented it from fulfilling the request. ' .
    '(This error code is needed because a 500 Internal Server ' .
    'Error HTTP status code cannot be returned to the client ' .
    'via an HTTP redirect.)';
}
