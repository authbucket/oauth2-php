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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorization related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationServiceProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {
    $app['oauth2.auth.default_options'] = array(
      'response_type' => array(
        'Pantarei\OAuth2\Extension\ResponseType\CodeResponseType',
        'Pantarei\OAuth2\Extension\ResponseType\TokenResponseType',
      ),
    );

    $app['oauth2.auth.options.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return;
      }
      $initialized = TRUE;

      if (!isset($app['oauth2.auth.options'])) {
        $app['oauth2.auth.options'] = $app['oauth2.auth.default_options'];
      }
    });

    $app['oauth2.auth.response_type'] = $app->share(function ($app) {
      $app['oauth2.auth.options.initializer']();

      $request = Request::createFromGlobals();
      $query = $request->query->all();

      // Prepare the filtered query.
      $filtered_query = $app['oauth2.param.filter']($query, array('client_id', 'redirect_uri', 'response_type', 'scope', 'state'));

      // response_type is required.
      if (!isset($filtered_query['response_type'])) {
        if (isset($query['response_type'])) {
          throw new UnsupportedResponseTypeException();
        }
        throw new InvalidRequestException();
      }

      $response_type = NULL;
      foreach ($app['oauth2.auth.options']['response_type'] as $namespace) {
        $response_type = new $namespace($app);
        if ($response_type->getName() === $query['response_type']) {
          $response_type->buildType($query, $filtered_query);
          break;
        }
      }
      return $response_type;
    });
  }

  public function boot(Application $app)
  {
  }
}
