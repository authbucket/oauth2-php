<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Request;

use Pantarei\Oauth2\Exception\AccessDeniedException;
use Pantarei\Oauth2\Exception\InvalidRequestException;
use Pantarei\Oauth2\Exception\InvalidScopeException;
use Pantarei\Oauth2\Exception\ServerErrorException;
use Pantarei\Oauth2\Exception\TemporarilyUnavailableException;
use Pantarei\Oauth2\Exception\UnauthorizedClientException;
use Pantarei\Oauth2\Exception\UnsupportedResponseTypeException;
use Pantarei\Oauth2\Oauth2;

/**
 * Authorization request implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationRequest implements RequestInterface
{
  public function validateRequest(array $query = array())
  {
    $filters = array(
      'response_type' => array(
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags' => FILTER_REQUIRE_SCALAR,
        'options' => array(
          'regexp' => Oauth2::getRegexp('response_type')
        ),
      ),
      'client_id' => array(
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags' => FILTER_REQUIRE_SCALAR,
        'options' => array(
          'regexp' => Oauth2::getRegexp('client_id'),
        ),
      ),
      'redirect_uri' => array(
        'filter' => FILTER_SANITIZE_URL
      ),
      'scope' => array(
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags' => FILTER_REQUIRE_SCALAR,
        'options' => array(
          'regexp' => Oauth2::getRegexp('scope'),
        ),
      ),
      'state' => array(
          'filter' => FILTER_VALIDATE_REGEXP,
          'flags' => FILTER_REQUIRE_SCALAR,
          'options' => array(
            'regexp' => Oauth2::getRegexp('state'),
          ),
        ),
      );

    $filtered_query = filter_var_array($query, $filters);

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
