<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\ResponseType;

use Silex\Application;

/**
 * Token response type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseType implements ResponseTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "token".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  private $responseType = 'token';

  /**
   * REQUIRED. The client identifier as described in Section 2.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  private $clientId = '';

  /**
   * OPTIONAL. As described in Section 3.1.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  private $redirectUri = '';

  /**
   * OPTIONAL. The scope of the access request as described by
   * Section 3.3.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  private $scope = '';

  /**
   * RECOMMENDED. An opaque value used by the client to maintain
   * state between the request and callback. The authorization
   * server includes this value when redirecting the user-agent back
   * to the client. The parameter SHOULD be used for preventing
   * cross-site request forgery as described in Section 10.12.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  private $state = '';

  public function getResponseType()
  {
    return $this->responseType;
  }

  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
    return $this;
  }

  public function getClientId()
  {
    return $this->clientId;
  }

  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
    return $this;
  }

  public function getRedirectUri()
  {
    return $this->redirectUri;
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

  public function __construct(Application $app, $query, $filtered_query)
  {
    // Validate and set client_id.
    if ($app['param.check.client_id']($query, $filtered_query)) {
      $this->setClientId($query['client_id']);
    }

    // Validate and set redirect_uri. NOTE: redirect_uri is not required if
    // already established via other channels.
    $query = $app['param.fetch.redirect_uri']($query);
    if ($app['param.check.redirect_uri']($query, $filtered_query)) {
      $this->setRedirectUri($query['redirect_uri']);
    }

    // Validate and set scope.
    if ($app['param.check.scope']($query, $filtered_query)) {
      $this->setScope($query['scope']);
    }

    // Validate and set state.
    if ($app['param.check.state']($query, $filtered_query)) {
      $this->setState($query['state']);
    }
  }
}
