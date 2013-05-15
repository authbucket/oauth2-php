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
    $app['param.filter.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return;
      }
      $initialized = TRUE;

      if (!isset($app['param.filter.syntax'])) {
        $app['param.filter.syntax'] = array(
          'VSCHAR'            => '[\x20-\x7E]',
          'NQCHAR'            => '[\x21\x22-\x5B\x5D-\x7E]',
          'NQSCHAR'           => '[\x20-\x21\x23-\x5B\x5D-\x7E]',
          'UNICODECHARNOCRLF' => '[\x09\x20-\x7E\x80-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]',
        );
      }
      $syntax = $app['param.filter.syntax'];

      if (!isset($app['param.filter.regexp'])) {
        $app['param.filter.regexp'] = array(
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
      }
      $regexp = $app['param.filter.regexp'];

      if (!isset($app['param.filter.definition'])) {
        $app['param.filter.definition'] = array(
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

    $app['param.filter'] = $app->protect(function ($query, $params = NULL) use ($app) {
      // Initialize filter definition.
      $app['param.filter.initializer']();

      $filtered_query = array_filter(filter_var_array($query, $app['param.filter.definition']));

      // Return entire result set, or only specific key(s).
      if ($params != NULL) {
        return array_intersect_key($filtered_query, array_flip($params));
      }
      return $filtered_query;
    });

    $app['param.check.client_id'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // client_id is required and must in good format.
      if (!isset($filtered_query['client_id']) && !isset($query['client_id'])) {
        throw new InvalidRequestException();
      }

      // If client_id is invalid we should stop here.
      $client = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $query['client_id'],
      ));
      if ($client == NULL) {
        throw new UnauthorizedClientException();
      }

      return TRUE;
    });

    $app['param.check.code'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // code is required and must in good format.
      if (!isset($filtered_query['code'])) {
        throw new InvalidRequestException();
      }

      // If refresh_token is invalid we should stop here.
      $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Codes')->findOneBy(array(
        'code' => $filtered_query['code'],
      ));
      if ($result == NULL) {
        throw new InvalidGrantException();
      }
      elseif ($result->getExpires() < time()) {
        throw new InvalidRequestException();
      }

      return TRUE;
    });

    $app['param.fetch.redirect_uri'] = $app->protect(function ($query) use ($app) {
      // redirect_uri is not required if already established via other channels,
      // check an existing redirect URI against the one supplied.
      $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $query['client_id'],
      ));
      if ($result !== NULL && $result->getRedirectUri()) {
        $query['redirect_uri'] = $result->getRedirectUri();
      }
      return $query;
    });

    $app['param.check.redirect_uri'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // At least one of: existing redirect URI or input redirect URI must be
      // specified.
      if (!isset($filtered_query['redirect_uri']) && !isset($query['redirect_uri'])) {
        throw new InvalidRequestException();
      }

      // If there's an existing uri and one from input, verify that they match.
      if (isset($filtered_query['redirect_uri']) && isset($query['redirect_uri'])) {
        // Ensure that the input uri starts with the stored uri.
        if (strcasecmp(substr($filtered_query["redirect_uri"], 0, strlen($query['redirect_uri'])), $query['redirect_uri']) !== 0) {
          throw new InvalidRequestException();
        }
      }

      return TRUE;
    });

    $app['param.check.refresh_token'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // refresh_token is required and must in good format.
      if (!isset($filtered_query['refresh_token'])) {
        throw new InvalidRequestException();
      }

      // If refresh_token is invalid we should stop here.
      $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\RefreshTokens')->findOneBy(array(
        'refresh_token' => $filtered_query['refresh_token'],
      ));
      if ($result == NULL) {
        throw new InvalidGrantException();
      }
      elseif ($result->getExpires() < time()) {
        throw new InvalidRequestException();
      }

      return TRUE;
    });

    $app['param.check.scope'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // scope is optional.
      if (isset($query['scope'])) {
        if (!isset($filtered_query['scope'])) {
          throw new InvalidScopeException();
        }

        // Check scope from database.
        foreach (preg_split("/\s+/", $filtered_query['scope']) as $scope) {
          $result = $app['orm']->getRepository('Pantarei\OAuth2\Entity\Scopes')->findOneBy(array(
            'scope' => $scope,
          ));
          if ($result == NULL) {
            throw new InvalidScopeException();
          }
        }
        return TRUE;
      }
      return FALSE;
    });

    $app['param.check.state'] = $app->protect(function ($query, $filtered_query) use ($app) {
      if (isset($query['state'])) {
        if (!isset($filtered_query['state'])) {
          throw new InvalidRequestException();
        }
        return TRUE;
      }
      return FALSE;
    });
  }

  public function boot(Application $app)
  {
  }
}
