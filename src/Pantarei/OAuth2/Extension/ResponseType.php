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
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Pantarei\OAuth2\OAuth2TypeInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the abstract class for response type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ResponseType implements OAuth2TypeInterface
{
  protected $client_id = '';

  protected $username = '';

  protected $scope = array();

  abstract public static function create(Request $request, Application $app);

  abstract public function getResponse(Request $request, Application $app);

  public function setClientId($client_id)
  {
    $this->client_id = $client_id;
    return $this;
  }

  public function getClientId()
  {
    return $this->client_id;
  }

  public function setUsername($username)
  {
    $this->username = $username;
    return $this;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function setScope($scope)
  {
    $this->scope = $scope;
    return $this;
  }

  public function getScope()
  {
    return $this->scope;
  }

  public static function getType(Request $request, Application $app)
  {
    // Prepare the filtered query.
    $params = array('client_id', 'redirect_uri', 'response_type', 'scope', 'state');
    $filtered_query = ParameterUtils::filter($request->query->all(), $params);
    foreach ($params as $param) {
      if ($request->query->get($param)) {
        if (!isset($filtered_query[$param]) || $filtered_query[$param] !== $request->query->get($param)) {
          throw new InvalidRequestException();
        }
      }
    }

    // response_type is required.
    if (!isset($filtered_query['response_type'])) {
      throw new InvalidRequestException();
    }
    $response_type = $filtered_query['response_type'];

    // Check if response_type is supported.
    if (!isset($app['oauth2.auth.options']['response_type'][$response_type])) {
      throw new UnsupportedResponseTypeException();
    }

    // Create and return the controller.
    $namespace = $app['oauth2.auth.options']['response_type'][$response_type];
    return $namespace::create($request, $app);
  }
}
