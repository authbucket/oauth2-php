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
use Pantarei\OAuth2\Extension\ResponseType;
use Pantarei\OAuth2\Extension\TokenType\BearerTokenType;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Token response type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.2
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenResponseType extends ResponseType
{
  /**
   * REQUIRED. Value MUST be set to "token".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  protected $response_type = 'token';

  /**
   * OPTIONAL. As described in Section 3.1.2.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  protected $redirect_uri = '';

  /**
   * RECOMMENDED. An opaque value used by the client to maintain
   * state between the request and callback. The authorization
   * server includes this value when redirecting the user-agent back
   * to the client. The parameter SHOULD be used for preventing
   * cross-site request forgery as described in Section 10.12.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
   */
  protected $state = '';

  public function setRedirectUri($redirect_uri)
  {
    $this->redirect_uri = $redirect_uri;
    return $this;
  }

  public function getRedirectUri()
  {
    return $this->redirect_uri;
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

  public function __construct(Request $request, Application $app)
  {
    // REQUIRED: client_id.
    if (!$request->query->get('client_id')) {
      throw new InvalidRequestException();
    }

    // Validate and set client_id.
    if ($client_id = ParameterUtils::checkClientId($request, $app)) {
      $this->setClientId($client_id);
    }

    // Validate and set redirect_uri.
    if ($redirect_uri = ParameterUtils::checkRedirectUri($request, $app)) {
      $this->setRedirectUri($redirect_uri);
    }

    // Validate and set scope.
    if ($scope = ParameterUtils::checkScope($request, $app)) {
      $this->setScope($scope);
    }

    // Validate and set state.
    if ($state = $request->query->get('state')) {
      $this->setState($state);
    }
  }
  
  public static function create(Request $request, Application $app)
  {
    return new static($request, $app);
  }

  public function getResponse(Request $request, Application $app)
  {
    $response = BearerTokenType::create($request, $app);
    return $response->getResponse($request, $app);
  }
}
