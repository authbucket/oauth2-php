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

use Silex\Application;

/**
 * Refresh token grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-6
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantType implements GrantTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "refresh_token".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $grantType = 'refresh_token';

  /**
   * REQUIRED. The refresh token issued to the client.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $refreshToken = '';

  /**
   * OPTIONAL.  The scope of the access request as described by
   * Section 3.3.  The requested scope MUST NOT include any scope
   * not originally granted by the resource owner, and if omitted is
   * treated as equal to the scope originally granted by the
   * resource owner.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $scope = '';

  public function getGrantType()
  {
    return $this->grantType;
  }

  public function setRefreshToken($refreshToken)
  {
    $this->refreshToken = $refreshToken;
    return $this;
  }

  public function getRefreshToken()
  {
    return $this->refreshToken;
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

  public function __construct(Application $app, $query, $filtered_query)
  {
    // Validate and set refresh_token.
    if ($app['oauth2.param.check.refresh_token']($query, $filtered_query)) {
      $this->setRefreshToken($query['refresh_token']);
    }

    // Validate and set scope.
    if ($app['oauth2.param.check.scope']($query, $filtered_query)) {
      $this->setScope($query['scope']);
    }
  }
}
