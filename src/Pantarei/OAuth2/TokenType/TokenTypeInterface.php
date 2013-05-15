<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\TokenType;

/**
 * Defines the interface for token type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface TokenTypeInterface
{
  /**
   * Return the supported token type.
   *
   * The access token type provides the client with the information
   * required to successfully utilize the access token to make a protected
   * resource request (along with type-specific attributes). The client
   * MUST NOT use an access token if it does not understand the token
   * type.
   *
   * @return string
   *   The supported token type as defined in rfc6749, one of:
   *   - bearer
   *   - mac
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.2
   * @see http://tools.ietf.org/html/rfc6749#section-5.1
   * @see http://tools.ietf.org/html/rfc6749#section-7.1
   * @see http://tools.ietf.org/html/rfc6749#section-8.1
   */
  public function getTokenType();
}
