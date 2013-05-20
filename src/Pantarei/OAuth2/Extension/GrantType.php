<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\OAuth2TypeInterface;
use Pantarei\OAuth2\Util\CredentialUtils;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the abstract class for grant type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class GrantType implements OAuth2TypeInterface
{
  public function __construct(Request $request, Application $app)
  {
    return TRUE;
  }

  public function getResponse(Request $request, Application $app)
  {
    return new Response();
  }

  public function getParent()
  {
    return NULL;
  }

  public function getName()
  {
    return 'grant_type';
  }

  public static function getType(Request $request, Application $app)
  {
    $query = $request->request->all();

    // Prepare the filtered query.
    $params = array('client_id', 'code', 'grant_type', 'password', 'redirect_uri', 'refresh_token', 'scope', 'username');
    $filtered_query = ParameterUtils::filter($request->request->all(), $params);
    foreach ($params as $param) {
      if (isset($query[$param])) {
        if (!isset($filtered_query[$param]) || $filtered_query[$param] !== $request->request->get($param)) {
          throw new InvalidRequestException();
        }
      }
    }

    // grant_type is required.
    if (!isset($filtered_query['grant_type'])) {
      throw new InvalidRequestException();
    }
    $grant_type = $filtered_query['grant_type'];

    // Check if grant_type is supported.
    if (!isset($app['oauth2.token.options']['grant_type'][$grant_type])) {
      throw new UnsupportedGrantTypeException();
    }

    // Validate client_id.
    CredentialUtils::check($request, $app);

    // Create and return the token type.
    $namespace = $app['oauth2.token.options']['grant_type'][$grant_type];
    return new $namespace($request, $app);
  }
}
