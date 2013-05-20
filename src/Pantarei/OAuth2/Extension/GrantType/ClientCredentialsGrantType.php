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
use Pantarei\OAuth2\Extension\GrantType;
use Pantarei\OAuth2\Util\ParameterUtils;
use Rhumsaa\Uuid\Uuid;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
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
  protected $grant_type = 'client_credentials';

  public function __construct(Request $request, Application $app)
  {
    // Validate and set client_id.
    if ($client_id = ParameterUtils::checkClientId($request, $app, 'POST')) {
      $this->setClientId($client_id);
    }

    // Validate and set scope.
    if ($scope = ParameterUtils::checkScope($request, $app, 'POST')) {
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
