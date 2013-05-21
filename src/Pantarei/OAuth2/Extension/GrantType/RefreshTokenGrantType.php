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

use Pantarei\OAuth2\Entity\AccessTokens;
use Pantarei\OAuth2\Entity\RefreshTokens;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Extension\GrantType;
use Pantarei\OAuth2\Util\ParameterUtils;
use Rhumsaa\Uuid\Uuid;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
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
  protected $grant_type = 'refresh_token';

  /**
   * REQUIRED. The refresh token issued to the client.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   */
  protected $refresh_token = '';

  public function setRefreshToken($refresh_token)
  {
    $this->refresh_token = $refresh_token;
    return $this;
  }

  public function getRefreshToken()
  {
    return $this->refresh_token;
  }

  public function __construct(Request $request, Application $app)
  {
    // REQUIRED: refresh_token.
    if (!$request->request->get('refresh_token')) {
      throw new InvalidRequestException();
    }

    // Validate and set client_id.
    if ($client_id = ParameterUtils::checkClientId($request, $app)) {
      $this->setClientId($client_id);
    }

    // Validate and set refresh_token.
    if ($refresh_token = ParameterUtils::checkRefreshToken($request, $app)) {
      $this->setRefreshToken($refresh_token);
    }

    // Validate and set scope.
    if ($scope = ParameterUtils::checkScopeByRefreshToken($request, $app)) {
      $this->setScope($scope);
    }
  }

  public static function create(Request $request, Application $app)
  {
    return new static($request, $app);
  }

  public function getResponse(Request $request, Application $app)
  {
    $access_token = new AccessTokens();
    $access_token->setAccessToken(md5(Uuid::uuid4()))
      ->setTokenType('bearer')
      ->setClientId($this->getClientId())
      ->setUsername('')
      ->setExpires(time() + 3600)
      ->setScope($this->getScope());
    $app['oauth2.orm']->persist($access_token);
    $app['oauth2.orm']->flush();

    $refresh_token = new RefreshTokens();
    $refresh_token->setRefreshToken(md5(Uuid::uuid4()))
      ->setTokenType('bearer')
      ->setClientId($this->getClientId())
      ->setUsername('')
      ->setExpires(time() + 86400)
      ->setScope($this->getScope());
    $app['oauth2.orm']->persist($refresh_token);
    $app['oauth2.orm']->flush();

    $parameters = array(
      'access_token' => $access_token->getAccessToken(),
      'token_type' => $access_token->getTokenType(),
      'expires_in' => $access_token->getExpires() - time(),
      'refresh_token' => $refresh_token->getRefreshToken(),
      'scope' => implode(' ', $this->getScope()),
    );
    $headers = array(
      'Cache-Control' => 'no-store',
      'Pragma' => 'no-cache',
    );
    $response = JsonResponse::create(array_filter($parameters), 200, $headers);

    return $response;
  }
}
