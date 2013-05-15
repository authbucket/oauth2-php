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

use Pantarei\OAuth2\Util\ResourceOwnerCredentialUtils;
use Silex\Application;

/**
 * Password grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantType implements GrantTypeInterface
{
  /**
   * REQUIRED. Value MUST be set to "password".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   */
  private $grantType = 'password';

  /**
   * REQUIRED. The resource owner username.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   */
  private $username = '';

  /**
   * REQUIRED. The resource owner password.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   */
  private $password = '';

  /**
   * OPTIONAL. The scope of the access request as described by
   * Section 3.3.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   */
  private $scope = '';

  public function getGrantType()
  {
    return $this->grantType;
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

  public function setPassword($password)
  {
    $this->password = $password;
    return $this;
  }

  public function getPassword()
  {
    return $this->password;
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

  public function __construct(Application $app, $query, $filtered_query) {
    // Validate and set username and password.
    if (ResourceOwnerCredentialUtils::check($app, $query, $filtered_query)) {
      $this->setUsername($filtered_query['username']);
      $this->setPassword($filtered_query['password']);
    }

    // Validate and set scope.
    if ($app['oauth2.param.check.scope']($query, $filtered_query)) {
      $this->setScope($query['scope']);
    }
  }
}
