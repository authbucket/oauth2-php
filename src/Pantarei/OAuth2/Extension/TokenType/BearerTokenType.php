<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\TokenType;

use Pantarei\OAuth2\Extension\TokenTypeInterface;
use Pantarei\OAuth2\Util\ParameterUtils;
use Rhumsaa\Uuid\Uuid;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bearer token type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenType implements TokenTypeInterface
{
    private $client_id = '';

    private $username = '';

    private $scope = array();

    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    public function getClientId()
    {
        return $this->client_id;
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

    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function __construct(Request $request, Application $app)
    {
        // Validate and set client_id.
        if ($client_id = ParameterUtils::checkClientId($request, $app)) {
            $this->setClientId($client_id);
        }

        // Validate and set scope.
        if ($scope = ParameterUtils::checkScopeByCode($request, $app)) {
            $this->setScope($scope);
        } elseif ($scope = ParameterUtils::checkScopeByRefreshToken($request, $app)) {
            $this->setScope($scope);
        } elseif ($scope = ParameterUtils::checkScope($request, $app)) {
            $this->setScope($scope);
        }

        // Fetch the current user.
        $token = $app['security']->getToken();
        $this->setUsername(null !== $token ? $token->getUser()->getUsername() : '');
    }

    public static function create(Request $request, Application $app)
    {
        return new static($request, $app);
    }

    public function getResponse(Request $request, Application $app)
    {
        $access_token = new $app['oauth2.entity.access_tokens']();
        $access_token->setAccessToken(md5(Uuid::uuid4()))
            ->setTokenType('bearer')
            ->setClientId($this->getClientId())
            ->setUsername($this->getUsername())
            ->setExpires(time() + 3600)
            ->setScope($this->getScope());
        $app['oauth2.orm']->persist($access_token);
        $app['oauth2.orm']->flush();

        $refresh_token = new $app['oauth2.entity.refresh_tokens']();
        $refresh_token->setRefreshToken(md5(Uuid::uuid4()))
            ->setTokenType('bearer')
            ->setClientId($this->getClientId())
            ->setUsername($this->getUsername())
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
