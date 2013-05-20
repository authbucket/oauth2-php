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

use Pantarei\OAuth2\Extension\ResponseType;
use Silex\Application;
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
      $response_type = ResponseType::getType($request, $app);
      return $response_type->getResponse($request, $app);
    });

    return $controllers;
  }
}
