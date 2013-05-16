<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\ResponseType;

use Pantarei\OAuth2\Extension\ResponseType;
use Silex\Application;

/**
 * Code response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseType extends ResponseType
{
  /**
   * REQUIRED. Value MUST be set to "code".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $response_type = 'code';

  /**
   * REQUIRED. The client identifier as described in Section 2.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $client_id = '';

  /**
   * OPTIONAL. As described in Section 3.1.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $redirect_uri = '';

  /**
   * OPTIONAL. The scope of the access request as described by Section 3.3.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $scope = '';

  /**
   * RECOMMENDED. An opaque value used by the client to maintain
   * state between the request and callback. The authorization
   * server includes this value when redirecting the user-agent back
   * to the client. The parameter SHOULD be used for preventing
   * cross-site request forgery as described in Section 10.12.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $state = '';

  public function setClientId($client_id)
  {
    $this->client_id = $client_id;
    return $this;
  }

  public function getClientId()
  {
    return $this->client_id;
  }

  public function setRedirectUri($redirect_uri)
  {
    $this->redirect_uri = $redirect_uri;
    return $this;
  }

  public function getRedirectUri()
  {
    return $this->redirect_uri;
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

  public function setState($state)
  {
    $this->state = $state;
    return $this;
  }

  public function getState()
  {
    return $this->state;
  }

  public function buildType($query, $filtered_query)
  {
    // Validate and set client_id.
    if ($this->app['oauth2.param.check.client_id']($query, $filtered_query)) {
      $this->setClientId($query['client_id']);
    }

    // Validate and set redirect_uri. NOTE: redirect_uri is not required if
    // already established via other channels.
    $query = $this->app['oauth2.param.fetch.redirect_uri']($query);
    if ($this->app['oauth2.param.check.redirect_uri']($query, $filtered_query)) {
      $this->setRedirectUri($query['redirect_uri']);
    }

    // Validate and set scope.
    if ($this->app['oauth2.param.check.scope']($query, $filtered_query)) {
      $this->setScope($query['scope']);
    }

    // Validate and set state.
    if ($this->app['oauth2.param.check.state']($query, $filtered_query)) {
      $this->setState($query['state']);
    }
  }

  public function getParent()
  {
    return 'response_type';
  }

  public function getName()
  {
    return $this->response_type;
  }
}
