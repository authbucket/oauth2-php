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

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Access token service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenServiceProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {
    $app['oauth2.token.default_options'] = array(
      'grant_type' => array(
        'authorization_code' => 'Pantarei\OAuth2\Extension\GrantType\AuthorizationCodeGrantType',
        'client_credentials' => 'Pantarei\OAuth2\Extension\GrantType\ClientCredentialsGrantType',
        'password' => 'Pantarei\OAuth2\Extension\GrantType\PasswordGrantType',
        'refresh_token' => 'Pantarei\OAuth2\Extension\GrantType\RefreshTokenGrantType',
      ),
    );

    $app['oauth2.token.options.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return FALSE;
      }
      $initialized = TRUE;

      if (!isset($app['oauth2.token.options'])) {
        $app['oauth2.token.options'] = $app['oauth2.token.default_options'];
      }
      return TRUE;
    });

    $app['oauth2.token.grant_type.config'] = $app->share(function($app) {
      $app['oauth2.token.options.initializer']();

      $configs = new \Pimple();
      foreach ($app['oauth2.token.options']['grant_type'] as $name => $options) {
        $configs[$name] = new $options($app);
      }
      return $configs;
    });

    $app['oauth2.token.grant_type'] = $app->share(function ($app) {
      $request = Request::createFromGlobals();
      $query = $request->request->all();

      // Prepare the filtered query.
      $params = array('client_id', 'code', 'grant_type', 'password', 'redirect_uri', 'refresh_token', 'scope', 'username');
      $filtered_query = $app['oauth2.param.filter']($query, $params);
      foreach ($params as $param) {
        if (isset($query[$param])) {
          if (!isset($filtered_query[$param]) || $filtered_query[$param] !== $query[$param]) {
            throw new InvalidRequestException();
          }
        }
      }

      // grant_type is required.
      if (!isset($filtered_query['grant_type'])) {
        throw new InvalidRequestException();
      }

      // Check if response_type is supported.
      if (!isset($app['oauth2.token.grant_type.config'][$query['grant_type']])) {
        throw new UnsupportedGrantTypeException();
      }

      // Validate and set client_id.
      $query = $app['oauth2.credential.fetch.client']($query);
      if (!$app['oauth2.credential.check.client']($query, $filtered_query)) {
        throw new InvalidClientException();
      }

      // Create, build and return the token type.
      $grant_type = $app['oauth2.token.grant_type.config'][$query['grant_type']];
      $grant_type->buildType($query, $filtered_query);
      return $grant_type;
    });
  }

  public function boot(Application $app)
  {
  }
}
