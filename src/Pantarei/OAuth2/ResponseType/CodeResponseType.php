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

use Pantarei\OAuth2\Util\ClientIdUtils;
use Pantarei\OAuth2\Util\RedirectUriUtils;
use Pantarei\OAuth2\Util\ScopeUtils;
use Pantarei\OAuth2\Util\StateUtils;
use Silex\Application;

/**
 * Code response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseType implements ResponseTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "code".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $responseType = 'code';

  /**
   * REQUIRED. The client identifier as described in Section 2.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $clientId = '';

  /**
   * OPTIONAL. As described in Section 3.1.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $redirectUri = '';

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
    if (ClientIdUtils::check($app, $query, $filtered_query)) {
      $this->setClientId($query['client_id']);
    }

    // Validate and set redirect_uri. NOTE: redirect_uri is not required if
    // already established via other channels.
    $query = RedirectUriUtils::fetch($app, $query);
    if (RedirectUriUtils::check($app, $query, $filtered_query)) {
      $this->setRedirectUri($query['redirect_uri']);
    }

    // Validate and set scope.
    if (ScopeUtils::check($app, $query, $filtered_query)) {
      $this->setScope($query['scope']);
    }

    // Validate and set state.
    if (StateUtils::check($app, $query, $filtered_query)) {
      $this->setState($query['state']);
    }
  }
}
