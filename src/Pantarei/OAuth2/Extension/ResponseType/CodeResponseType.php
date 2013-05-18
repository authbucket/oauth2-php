<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\ResponseType;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Pantarei\OAuth2\Extension\ResponseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Code response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseType extends ResponseType
{
  /**
   * REQUIRED. Value MUST be set to "code".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $response_type = 'code';

  /**
   * REQUIRED. The client identifier as described in Section 2.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $client_id = '';

  /**
   * OPTIONAL. As described in Section 3.1.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $redirect_uri = '';

  /**
   * OPTIONAL. The scope of the access request as described by Section 3.3.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $scope = '';

  /**
   * RECOMMENDED. An opaque value used by the client to maintain
   * state between the request and callback. The authorization
   * server includes this value when redirecting the user-agent back
   * to the client. The parameter SHOULD be used for preventing
   * cross-site request forgery as described in Section 10.12.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
   */
  private $state = '';

  public function setClientId($client_id)
  {
    $this->client_id = $client_id;
    return $this;
  }

  public function getClientId()
  {
    return $this->client_id;
  }

  public function setRedirectUri($redirect_uri)
  {
    $this->redirect_uri = $redirect_uri;
    return $this;
  }

  public function getRedirectUri()
  {
    return $this->redirect_uri;
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

  public function setState($state)
  {
    $this->state = $state;
    return $this;
  }

  public function getState()
  {
    return $this->state;
  }

  public function buildType()
  {
    $request = Request::createFromGlobals();

    // client_id is required.
    if (!$request->query->get('client_id')) {
      throw new InvalidRequestException();
    }

    // Validate client_id.
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
      'client_id' => $request->query->get('client_id'),
    ));
    if ($result === NULL) {
      throw new UnauthorizedClientException();
    }

    // redirect_uri is not required if already established via other channels,
    // check an existing redirect URI against the one supplied.
    $redirect_uri = NULL;
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
      'client_id' => $request->query->get('client_id'),
    ));
    if ($result !== NULL && $result->getRedirectUri()) {
      $redirect_uri = $result->getRedirectUri();
    }

    // At least one of: existing redirect URI or input redirect URI must be
    // specified.
    if (!$redirect_uri && !$request->query->get('redirect_uri')) {
      throw new InvalidRequestException();
    }
    
    // If there's an existing uri and one from input, verify that they match.
    if ($redirect_uri && $request->query->get('redirect_uri')) {
      // Ensure that the input uri starts with the stored uri.
      if (strcasecmp(substr($request->query->get('redirect_uri'), 0, strlen($redirect_uri)), $redirect_uri) !== 0) {
        throw new InvalidRequestException();
      }
    }

    // scope is optional.
    if ($request->query->get('scope')) {
      // Check scope with database record.
      foreach (preg_split('/\s+/', $request->query->get('scope')) as $scope) {
        $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Scopes')->findOneBy(array(
          'scope' => $scope,
        ));
        if ($result === NULL) {
          throw new InvalidScopeException();
        }
      }
    }

    // client_id is required.
    $this->setClientId($request->query->get('client_id'));

    // Set redirect_uri from database record, or directly from GET.
    if ($redirect_uri) {
      $this->setRedirectUri($redirect_uri);
    }
    else {
      $this->setRedirectUri($request->query->get('redirect_uri'));
    }

    // scope is optional.
    if ($request->query->get('scope')) {
      $this->setScope($request->query->get('scope'));
    }

    // state is optional.
    if ($request->query->get('state')) {
      $this->setScope($request->query->get('state'));
    }
  }

  public function getParent()
  {
    return 'response_type';
  }

  public function getName()
  {
    return $this->response_type;
  }
}
