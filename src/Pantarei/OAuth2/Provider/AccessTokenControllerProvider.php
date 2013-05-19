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

use Pantarei\OAuth2\Extension\GrantType;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Access token service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenControllerProvider implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $app['oauth2.token.default_options'] = array(
      'grant_type' => array(
        'authorization_code' => 'Pantarei\OAuth2\Extension\GrantType\AuthorizationCodeGrantType',
        'client_credentials' => 'Pantarei\OAuth2\Extension\GrantType\ClientCredentialsGrantType',
        'password' => 'Pantarei\OAuth2\Extension\GrantType\PasswordGrantType',
        'refresh_token' => 'Pantarei\OAuth2\Extension\GrantType\RefreshTokenGrantType',
      ),
    );

    if (!isset($app['oauth2.token.options'])) {
      $app['oauth2.token.options'] = $app['oauth2.token.default_options'];
    }

    $controllers = $app['controllers_factory'];

    // The main callback for access token endpoint.
    $controllers->post('/', function (Request $request, Application $app) {
      $grant_type = GrantType::getType($request, $app);
      return $grant_type->getResponse();
    });

    return $controllers;
  }
}
