<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Util;

/**
 * Parameter related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ParamUtils
{
  /**
   * Filter definition for filter_var_array();
   */
  static protected $definition = NULL;

  /**
   * Simply initialize self::$definition once.
   */
  final protected static function initializeDefinition()
  {
    $syntax = array(
      'VSCHAR'            => '[\x20-\x7E]',
      'NQCHAR'            => '[\x21\x22-\x5B\x5D-\x7E]',
      'NQSCHAR'           => '[\x20-\x21\x23-\x5B\x5D-\x7E]',
      'UNICODECHARNOCRLF' => '[\x09\x20-\x7E\x80-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]',
    );
    $regexp = array(
      'client_id'         => '/^(' . $syntax['VSCHAR'] . '*)$/',
      'client_secret'     => '/^(' . $syntax['VSCHAR'] . '*)$/',
      'response_type'     => '/^(code|token)$/',
      'scope'             => '/^(' . $syntax['NQCHAR'] . '+(?:\s*' . $syntax['NQCHAR'] . '+(?R)*)*)$/',
      'state'             => '/^(' . $syntax['VSCHAR'] . '+)$/',
      'error'             => '/^(' . $syntax['NQCHAR'] . '+)$/',
      'error_description' => '/^(' . $syntax['NQCHAR'] . '+)$/',
      'grant_type'        => '/^(client_credentials|password|authorization_code|refresh_token)$/',
      'code'              => '/^(' . $syntax['VSCHAR'] . '+)$/',
      'access_token'      => '/^(' . $syntax['VSCHAR'] . '+)$/',
      'token_type'        => '/^(bearer|mac)$/',
      'expires_in'        => '/^[0-9]+$/',
      'username'          => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
      'password'          => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
      'refresh_token'     => '/^(' . $syntax['VSCHAR'] . '+)$/',
    );
    $definition = array(
      'client_id'         => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['client_id'])),
      'client_secret'     => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['client_secret'])),
      'response_type'     => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['response_type'])),
      'scope'             => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['scope'])),
      'state'             => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['state'])),
      'redirect_uri'      => array('filter' => FILTER_SANITIZE_URL),
      'error'             => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['error'])),
      'error_description' => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['error_description'])),
      'error_uri'         => array('filter' => FILTER_SANITIZE_URL),
      'grant_type'        => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['grant_type'])),
      'code'              => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['code'])),
      'access_token'      => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['access_token'])),
      'token_type'        => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['token_type'])),
      'expires_in'        => array('filter' => FILTER_VALIDATE_INT),
      'username'          => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['username'])),
      'password'          => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['password'])),
      'refresh_token'     => array('filter' => FILTER_VALIDATE_REGEXP, 'flags' => FILTER_REQUIRE_SCALAR, 'options' => array('regexp' => $regexp['refresh_token'])),
    );

    self::$definition = $definition;
  }

  /**
   * Get a parameter from passed input query, with pattern filtering.
   *
   * @param array $query
   *   The input query for filtering.
   * @param array $params
   *   The target parameter in array or keys.
   *
   * @return array
   *   Filtered parameters.
   *
   * @see http://tools.ietf.org/html/rfc6749#appendix-A
   */
  final public static function filter($query, $params = NULL)
  {
    // Initialize self::$definition once if required.
    if (empty(self::$definition)) {
      self::initializeDefinition();
    }
    $filtered_query = array_filter(filter_var_array($query, self::$definition));

    // Return entire result set, or only specific key(s).
    if ($params != NULL) {
      return array_intersect_key($filtered_query, array_flip($params));
    }
    return $filtered_query;
  }
}
