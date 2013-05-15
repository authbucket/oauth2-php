<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\GrantType;

/**
 * Defines the interface for grant type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface GrantTypeInterface
{
  /**
   * Return the supported grant type.
   *
   * To request an access token, the client obtains authorization from the
   * resource owner. The authorization is expressed in the form of an
   * authorization grant, which the client uses to request the access
   * token. OAuth defines four grant types: authorization code, implicit,
   * resource owner password credentials, and client credentials. It also
   * provides an extension mechanism for defining additional grant types.
   *
   * @return string
   *   The supported grant type as defined in rfc6749, one of:
   *   - authorization_code
   *   - client_credentials
   *   - password
   *   - refresh_token
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   * @see http://tools.ietf.org/html/rfc6749#section-4.4.2
   * @see http://tools.ietf.org/html/rfc6749#section-4.5
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  public function getGrantType();
}
