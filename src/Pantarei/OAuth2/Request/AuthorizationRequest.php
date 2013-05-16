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

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Pantarei\OAuth2\Extension\ResponseType\CodeResponseType;
use Pantarei\OAuth2\Extension\ResponseType\TokenResponseType;
use Silex\Application;

/**
 * Authorization request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequest implements Request
{
  /**
   * Validate the authorization request.
   *
   * @todo Support defining new authorization endpoint response types.
   *
   * @return object
   *   The corresponding created response type object.
   */
  public function validateRequest(Application $app)
  {
    // Prepare the filtered query.
    $filtered_query = $app['oauth2.param.filter']($_GET, array('client_id', 'redirect_uri', 'response_type', 'scope', 'state'));

    // response_type is required.
    if (!isset($filtered_query['response_type'])) {
      if (isset($_GET['response_type'])) {
        throw new UnsupportedResponseTypeException();
      }
      throw new InvalidRequestException();
    }

    // Create and return the response type created.
    $response_type = NULL;
    switch ($_GET['response_type']) {
      case 'code':
        $response_type = new CodeResponseType($app);
        $response_type->buildType($_GET, $filtered_query);
        break;
      case 'token':
        $response_type = new TokenResponseType($app);
        $response_type->buildType($_GET, $filtered_query);
        break;
    }
    return $response_type;
  }
}
