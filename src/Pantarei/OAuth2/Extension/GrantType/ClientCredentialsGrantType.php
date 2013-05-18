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

use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Extension\GrantType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client credentials grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.4.2
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientCredentialsGrantType extends GrantType
{
  /**
   * REQUIRED. Value MUST be set to "client_credentials".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.4.2
   */
  private $grant_type = 'client_credentials';

  /**
   * OPTIONAL. The scope of the access request as described by
   * Section 3.3.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.4.2
   */
  private $scope = '';

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

    // scope is optionale
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
