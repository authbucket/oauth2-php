<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Provider;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * A simple Doctrine ORM service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ParameterServiceProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {
    $app['oauth2.param.filter.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return;
      }
      $initialized = TRUE;

      if (!isset($app['oauth2.param.filter.syntax'])) {
        $app['oauth2.param.filter.syntax'] = array(
          'VSCHAR'            => '[\x20-\x7E]',
          'NQCHAR'            => '[\x21\x22-\x5B\x5D-\x7E]',
          'NQSCHAR'           => '[\x20-\x21\x23-\x5B\x5D-\x7E]',
          'UNICODECHARNOCRLF' => '[\x09\x20-\x7E\x80-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]',
        );
      }
      $syntax = $app['oauth2.param.filter.syntax'];

      if (!isset($app['oauth2.param.filter.regexp'])) {
        $app['oauth2.param.filter.regexp'] = array(
          'client_id'         => '/^(' . $syntax['VSCHAR'] . '*)$/',
          'client_secret'     => '/^(' . $syntax['VSCHAR'] . '*)$/',
          'response_type'     => '/^([a-z0-9\_]+)$/',
          'scope'             => '/^(' . $syntax['NQCHAR'] . '+(?:\s*' . $syntax['NQCHAR'] . '+(?R)*)*)$/',
          'state'             => '/^(' . $syntax['VSCHAR'] . '+)$/',
          'error'             => '/^(' . $syntax['NQCHAR'] . '+)$/',
          'error_description' => '/^(' . $syntax['NQCHAR'] . '+)$/',
          'grant_type'        => '/^([a-z0-9\_\-\.]+)$/',
          'code'              => '/^(' . $syntax['VSCHAR'] . '+)$/',
          'access_token'      => '/^(' . $syntax['VSCHAR'] . '+)$/',
          'token_type'        => '/^([a-z0-9\_\-\.]+)$/',
          'expires_in'        => '/^([0-9]+)$/',
          'username'          => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
          'password'          => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
          'refresh_token'     => '/^(' . $syntax['VSCHAR'] . '+)$/',
        );
      }
      $regexp = $app['oauth2.param.filter.regexp'];

      if (!isset($app['oauth2.param.filter.definition'])) {
        $app['oauth2.param.filter.definition'] = array(
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
      }
    });

    $app['oauth2.param.filter'] = $app->protect(function ($query, $params = NULL) use ($app) {
      // Initialize filter definition.
      $app['oauth2.param.filter.initializer']();

      $filtered_query = array_filter(filter_var_array($query, $app['oauth2.param.filter.definition']));

      // Return entire result set, or only specific key(s).
      if ($params != NULL) {
        return array_intersect_key($filtered_query, array_flip($params));
      }
      return $filtered_query;
    });
  }

  public function boot(Application $app)
  {
  }
}
