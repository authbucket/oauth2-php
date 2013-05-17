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

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedScopeException;
use Pantarei\OAuth2\Extension\GrantType;
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

    // username and password are required.
    if (!$request->request->get('username') || !$request->request->get('password')) {
      throw new InvalidRequestException();
    }

    // If username and password invalid we should stop here.
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Users')->findOneBy(array(
      'username' => $request->request->get('username'),
      'password' => $request->request->get('password'),
    ));
    if ($result == NULL) {
      throw new InvalidGrantException();
    }

    // scope is optional.
    if ($request->request->get('scope')) {
      // Check scope with database record.
      foreach (preg_split('/\s+/', $request->request->get('scope')) as $scope) {
        $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Scopes')->findOneBy(array(
          'scope' => $scope,
        ));
        if ($result === NULL) {
          throw new InvalidScopeException();
        }
      }
    }

    // username and password are required.
    $this->setUsername($request->request->get('username'));
    $this->setPassword($request->request->get('password'));

    // state is optional.
    if ($request->query->get('state')) {
      $this->setScope($request->request->get('state'));
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
