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
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\Exception\TemporarilyUnavailableException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Pantarei\OAuth2\Util\ParamUtils;

/**
 * Authorization request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    $filtered_query = ParamUtils::filter($query, array('response_type', 'client_id', 'redirect_uri', 'scope', 'state'));

    // Both response_type and client_id are required.
    if (!$filtered_query['response_type'] || !$filtered_query['client_id']) {
      if (isset($query['response_type'])) {
        throw new UnsupportedResponseTypeException();
      }
      throw new InvalidRequestException();
    }

    // redirect_uri is not required if already established via other channels
    // check an existing redirect URI against the one supplied.
    //
    // TODO: Check backend storage to retrive saved redirect_uri.
    $redirect_uri = FALSE;

    // At least one of: existing redirect URI or input redirect URI must be
    // specified.
    if (!$redirect_uri && !$filtered_query['redirect_uri']) {
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
