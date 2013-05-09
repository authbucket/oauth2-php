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

  public function __construct(array $query = array())
  {
    if (isset($query['client_id'])) {
      $this->setClientId($query['client_id']);
    }
    if (isset($query['redirect_uri'])) {
      $this->setRedirectUri($query['redirect_uri']);
    }
    if (isset($query['scope'])) {
      $this->setScope($query['scope']);
    }
    if (isset($query['state'])) {
      $this->setState($query['state']);
    }
  }

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
}
