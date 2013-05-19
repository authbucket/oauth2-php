<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\GrantType;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Extension\GrantType;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Password grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantType extends GrantType
{
  /**
   * REQUIRED. Value MUST be set to "password".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3.2
   */
  private $grant_type = 'password';

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

  public function buildType() {
    $request = Request::createFromGlobals();
    $query = $request->request->all();

    // REQUIRED: username, password.
    if (!isset($query['username']) || !isset($query['password'])) {
      throw new InvalidRequestException();
    }

    // Validate and set username.
    if (ParameterUtils::checkUsername($this->app, $query)) {
      $this->setUsername($query['username']);

      // Validate and set password.
      if (ParameterUtils::checkPassword($this->app, $query)) {
        $this->setPassword($query['password']);
      }
    }

    // Validate and set scope.
    if (isset($query['scope'])) {
      if (ParameterUtils::checkScope($this->app, $query)) {
        $this->setScope($query['scope']);
      }
    }

    // Validate and set state.
    if (isset($query['state'])) {
      $this->setScope($query['state']);
    }
  }

  public function getParent()
  {
    return 'grant_type';
  }

  public function getName()
  {
    return $this->grant_type;
  }
}
