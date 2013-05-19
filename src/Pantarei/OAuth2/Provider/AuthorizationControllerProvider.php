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
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorization controller provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationControllerProvider implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $app['oauth2.auth.default_options'] = array(
      'response_type' => array(
        'code' => 'Pantarei\OAuth2\Extension\ResponseType\CodeResponseType',
        'token' => 'Pantarei\OAuth2\Extension\ResponseType\TokenResponseType',
      ),
    );

    if (!isset($app['oauth2.auth.options'])) {
      $app['oauth2.auth.options'] = $app['oauth2.auth.default_options'];
    }

    $controllers = $app['controllers_factory'];

    // The main callback for authorization endpoint.
    $controllers->get('/', function (Request $request, Application $app) {
      $response_type = $this->getResponseType($request, $app);
      return $response_type->getResponse();
    });

    return $controllers;
  }

  private function getResponseType(Request $request, Application $app)
  {
    // Prepare the filtered query.
    $params = array('client_id', 'redirect_uri', 'response_type', 'scope', 'state');
    $filtered_query = ParameterUtils::filter($request->query->all(), $params);
    foreach ($params as $param) {
      if ($request->query->get($param)) {
        if (!isset($filtered_query[$param]) || $filtered_query[$param] !== $request->query->get($param)) {
          throw new InvalidRequestException();
        }
      }
    }

    // response_type is required.
    if (!isset($filtered_query['response_type'])) {
      throw new InvalidRequestException();
    }
    $response_type = $request->query->get('response_type');

    // Check if response_type is supported.
    if (!isset($app['oauth2.auth.options']['response_type'][$response_type])) {
      throw new UnsupportedResponseTypeException();
    }

    // Create and return the controller.
    $namespace = $app['oauth2.auth.options']['response_type'][$response_type];
    return new $namespace($request, $app);
  }
}
