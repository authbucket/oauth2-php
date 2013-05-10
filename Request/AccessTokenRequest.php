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

use Pantarei\OAuth2\Exception\AccessDeniedException;
use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\Exception\TemporarilyUnavailableException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Pantarei\OAuth2\Util\ParamUtils;

/**
 * Access token request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    $filtered_query = ParamUtils::filter($query, array(
      'client_id',
      'code',
      'grant_type',
      'password',
      'redirect_uri',
      'refresh_token',
      'scope',
      'username',
    ));

    // grant_type myst be specified.
    if (!$filtered_query['grant_type']) {
      throw new InvalidRequestException();
    }

    // TODO: Make sure we've implemented the requested grant type.
    //if (!in_array($input["grant_type"], $this->getSupportedGrantTypes()))
    //  $this->errorJsonResponse(OAUTH2_HTTP_BAD_REQUEST, OAUTH2_ERROR_UNSUPPORTED_GRANT_TYPE);

    // Validate that the requested expires_in is number.
    if (isset($query['expires_in']) && !$filtered_query['expires_in']) {
      throw new InvalidRequestException();
    }

    // Validate that the requested scope is supported.
    if (isset($query['scope']) && !$filtered_query['scope']) {
      throw new InvalidScopeException();
    }

    // Validate that the requested state is supproted.
    if (isset($query['state']) && !$filtered_query['state']) {
      throw new InvalidRequestException();
    }

    return $filtered_query;
  }

}
