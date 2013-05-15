<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Request;

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantType;
use Pantarei\OAuth2\GrantType\ClientCredentialsGrantType;
use Pantarei\OAuth2\GrantType\PasswordGrantType;
use Pantarei\OAuth2\GrantType\RefreshTokenGrantType;
use Pantarei\OAuth2\Provider\CredentialServiceProvider;
use Silex\Application;

/**
 * Access token request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequest implements Request
{
  /**
   * Validate the access token request.
   *
   * @todo Support defining new authorization grant types.
   *
   * @return object
   *   The corresponding created grant type object.
   */
  public function validateRequest(Application $app)
  {
    $app->register(new CredentialServiceProvider());

    // Prepare the filtered query.
    $filtered_query = $app['oauth2.param.filter']($_POST, array('client_id', 'code', 'grant_type', 'password', 'redirect_uri', 'refresh_token', 'scope', 'username'));

    // grant_type is required.
    if (!isset($filtered_query['grant_type'])) {
      if (isset($_POST['grant_type'])) {
        throw new InvalidGrantException();
      }
      throw new InvalidRequestException();
    }

    // Validate and set client_id.
    $_POST = $app['oauth2.credential.fetch.client']($_POST);
    if (!$app['oauth2.credential.check.client']($_POST, $filtered_query)) {
      throw new InvalidClientException();
    }

    // Create and return the grant type created.
    $grant_type = NULL;
    switch ($filtered_query['grant_type']) {
      case 'authorization_code':
        $grant_type = new AuthorizationCodeGrantType($app, $_POST, $filtered_query);
        break;
      case 'client_credentials':
        $grant_type = new ClientCredentialsGrantType($app, $_POST, $filtered_query);
        break;
      case 'password':
        $grant_type = new PasswordGrantType($app, $_POST, $filtered_query);
        break;
      case 'refresh_token':
        $grant_type = new RefreshTokenGrantType($app, $_POST, $filtered_query);
        break;
    }
    return $grant_type;
  }
}
