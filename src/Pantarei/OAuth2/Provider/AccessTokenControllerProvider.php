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
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\Util\CredentialUtils;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Silex\ControllerCollection;
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
      $grant_type = $this->getGrantType($request, $app);
      return $grant_type->getResponse();
    });

    return $controllers;
  }

  private function getGrantType(Request $request, Application $app)
  {
    $query = $request->request->all();

    // Prepare the filtered query.
    $params = array('client_id', 'code', 'grant_type', 'password', 'redirect_uri', 'refresh_token', 'scope', 'username');
    $filtered_query = ParameterUtils::filter($query, $params);
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

    // Check if grant_type is supported.
    if (!isset($app['oauth2.token.options']['grant_type'][$query['grant_type']])) {
      throw new UnsupportedGrantTypeException();
    }

    // Validate and set client_id.
    if (!CredentialUtils::check($request, $app)) {
      throw new InvalidClientException();
    }

    // Create and return the token type.
    $grant_type = $app['oauth2.token.options']['grant_type'][$query['grant_type']];
    return new $grant_type($request, $app);
  }
}
