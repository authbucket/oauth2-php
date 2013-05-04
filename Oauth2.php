<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2;

/**
 * Shared component for Oauth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class Oauth2
{
  /**
   * Some of the definitions that follow use these common definitions.
   *
   * @see http://tools.ietf.org/html/rfc6749#appendix-A
   */
  protected static $syntax = array(
    'VSCHAR'            => '[\x20-\x7E]',
    'NQCHAR'            => '[\x21\x22-\x5B\x5D-\7E]',
    'NQSCHAR'           => '[\x20-\x21\x23-\x5B\x5D-\x7E]',
    'UNICODECHARNOCRLF' => '[\x09\x20-\x7E\x80-\xD7FF\xE000-\xFFFD\x10000-\x10FFFF]',
  );

  /**
   * Regexp for augmented Backus-Naur Form (ABNF) Syntax.
   *
   * @see http://tools.ietf.org/html/rfc6749#appendix-A
   */
  public static function getRegexp($element = '')
  {
    $regexp = array(
      'client_id'         => '/^(' . self::$syntax['VSCHAR'] . '*)$/',
      'client_secret'     => '/^(' . self::$syntax['VSCHAR'] . '*)$/',
      'response_type'     => '/^(code|token)$/',
      'scope'             => '/^(' . self::$syntax['NQCHAR'] . '+)$/',
      'state'             => '/^(' . self::$syntax['VSCHAR'] . '+)$/',
      'error'             => '/^(' . self::$syntax['NQCHAR'] . '+)$/',
      'error_description' => '/^(' . self::$syntax['NQCHAR'] . '+)$/',
      'grant_type'        => '/^(client_credentials|password|authorization_code|refresh_token)$/',
      'code'              => '/^(' . self::$syntax['VSCHAR'] . '+)$/',
      'access_token'      => '/^(' . self::$syntax['VSCHAR'] . '+)$/',
      'token_type'        => '/^(bearer|mac)$/',
      'expires_in'        => '/^[0-9]+$/',
      'username'          => '/^(' . self::$syntax['UNICODECHARNOCRLF'] . '*)$/',
      'password'          => '/^(' . self::$syntax['UNICODECHARNOCRLF'] . '*)$/',
      'refresh_token'     => '/^(' . self::$syntax['VSCHAR'] . '+)$/',
    );
    return isset($regexp[$element]) ? isset($regexp[$element]) : '';
  }
}
