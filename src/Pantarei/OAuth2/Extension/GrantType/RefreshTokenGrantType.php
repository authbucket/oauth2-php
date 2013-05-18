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
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Extension\GrantType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refresh token grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-6
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantType extends GrantType
{
  /**
   * REQUIRED. Value MUST be set to "refresh_token".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $grant_type = 'refresh_token';

  /**
   * REQUIRED. The refresh token issued to the client.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  private $refresh_token = '';

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

  public function setRefreshToken($refresh_token)
  {
    $this->refresh_token = $refresh_token;
    return $this;
  }

  public function getRefreshToken()
  {
    return $this->refresh_token;
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

  public function buildType()
  {
    $request = Request::createFromGlobals();

    // refresh_token is required.
    if (!$request->request->get('refresh_token')) {
      throw new InvalidRequestException();
    }

    // Validate refresh_token.
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\RefreshTokens')->findOneBy(array(
      'refresh_token' => $request->request->get('refresh_token'),
    ));
    if ($result === NULL) {
      throw new InvalidGrantException();
    }
    elseif ($result->getExpires() < time()) {
      throw new InvalidRequestException();
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

    // Set refresh_token.
    $this->setRefreshToken($request->request->get('refresh_token'));

    // scope is optional.
    if ($request->request->get('scope')) {
      $this->setScope($request->request->get('scope'));
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
