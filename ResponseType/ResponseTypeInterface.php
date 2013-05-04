<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\ResponseType;

/**
 * Defines the interface for response type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ResponseTypeInterface
{
  /**
   * Return the supported response type.
   *
   * The authorization endpoint is used by the authorization code grant
   * type and implicit grant type flows.  The client informs the
   * authorization server of the desired grant type using the following
   * parameter.
   *
   * @return string
   *   The supported response type as defined in rfc6749, one of:
   *   - code
   *   - token
   *
   * @see http://tools.ietf.org/html/rfc6749#section-3.1.1
   * @see http://tools.ietf.org/html/rfc6749#section-8.4
   */
  public function getResponseType();
}
