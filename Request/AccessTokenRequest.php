<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Request;

use Pantarei\Oauth2\Exception\AccessDeniedException;
use Pantarei\Oauth2\Exception\InvalidClientException;
use Pantarei\Oauth2\Exception\InvalidGrantException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Exception\ServerErrorException;
use Pantarei\Oauth2\Exception\TemporarilyUnavailableException;
use Pantarei\Oauth2\Exception\UnauthorizedClientException;
use Pantarei\Oauth2\Exception\UnsupportedGrantTypeException;
use Pantarei\Oauth2\Exception\UnsupportedResponseTypeException;
use Pantarei\Oauth2\Util\ParamUtils;

/**
 * Access token request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    $filtered_query = ParamUtils::filter($query, array('access_token', 'token_type', 'expires_in', 'scope', 'state'));

    // Both access_token and token_type are required.
    if (!$filtered_query['access_token'] || !$filtered_query['token_type']) {
      throw new InvalidRequestException();
    }

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
