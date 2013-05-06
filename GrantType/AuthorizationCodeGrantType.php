<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\GrantType;

/**
 * Authorization code grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantType implements GrantTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "authorization_code".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $grantType = 'authorization_code';

  /**
   * REQUIRED. The authorization code received from the
   * authorization server.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $code = '';

  /**
   * REQUIRED, if the "redirect_uri" parameter was included in the
   * authorization request as described in Section 4.1.1, and their
   * values MUST be identical.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $redirectUri;

  /**
   * REQUIRED, if the client is not authenticating with the
   * authorization server as described in Section 3.2.1.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $cilentId;

  public function __construct(array $query = array())
  {
    if (isset($query['code'])) {
      $this->setCode($query['code']);
    }
    if (isset($query['redirect_uri'])) {
      $this->setRedirectUri($query['redirect_uri']);
    }
    if (isset($query['client_id'])) {
      $this->setClientId($query['client_id']);
    }
  }

  public function getGrantType()
  {
    return $this->grantType;
  }

  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  public function getCode()
  {
    return $this->code;
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

  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
    return $this;
  }

  public function getClientId()
  {
    return $this->clientId;
  }
}
