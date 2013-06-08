<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Util;

use Doctrine\ORM\EntityRepository;
use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\InvalidScopeException;
use Pantarei\OAuth2\Exception\UnauthorizedClientException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\Exception\UnsupportedResponseTypeException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * A simple Doctrine ORM service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class ParameterUtils
{
    private static function initializer()
    {
        static $definition = null;

        if ($definition) {
            return $definition;
        }

        $syntax = array(
            'VSCHAR' => '[\x20-\x7E]',
            'NQCHAR' => '[\x21\x22-\x5B\x5D-\x7E]',
            'NQSCHAR' => '[\x20-\x21\x23-\x5B\x5D-\x7E]',
            'UNICODECHARNOCRLF' => '[\x09\x20-\x7E\x80-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]',
        );

        $regexp = array(
            'client_id' => '/^(' . $syntax['VSCHAR'] . '*)$/',
            'client_secret' => '/^(' . $syntax['VSCHAR'] . '*)$/',
            'response_type' => '/^([a-z0-9\_]+)$/',
            'scope' => '/^(' . $syntax['NQCHAR'] . '+(?:\s*' . $syntax['NQCHAR'] . '+(?R)*)*)$/',
            'state' => '/^(' . $syntax['VSCHAR'] . '+)$/',
            'error' => '/^(' . $syntax['NQCHAR'] . '+)$/',
            'error_description' => '/^(' . $syntax['NQCHAR'] . '+)$/',
            'grant_type' => '/^([a-z0-9\_\-\.]+)$/',
            'code' => '/^(' . $syntax['VSCHAR'] . '+)$/',
            'access_token' => '/^(' . $syntax['VSCHAR'] . '+)$/',
            'token_type' => '/^([a-z0-9\_\-\.]+)$/',
            'expires_in' => '/^([0-9]+)$/',
            'username' => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
            'password' => '/^(' . $syntax['UNICODECHARNOCRLF'] . '*)$/u',
            'refresh_token' => '/^(' . $syntax['VSCHAR'] . '+)$/',
        );

        $definition = array(
            'client_id' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['client_id'],
                ),
            ),
            'client_secret' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['client_secret'],
                ),
            ),
            'response_type' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['response_type'],
                ),
            ),
            'scope' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['scope'],
                ),
            ),
            'state' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['state'],
                ),
            ),
            'redirect_uri' => array(
                'filter' => FILTER_SANITIZE_URL),
            'error' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['error'],
                ),
            ),
            'error_description' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['error_description'],
                ),
            ),
            'error_uri' => array(
                'filter' => FILTER_SANITIZE_URL),
            'grant_type' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['grant_type'],
                ),
            ),
            'code' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['code'],
                ),
            ),
            'access_token' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['access_token'],
                ),
            ),
            'token_type' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['token_type'],
                ),
            ),
            'expires_in' => array(
                'filter' => FILTER_VALIDATE_INT),
            'username' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['username'],
                ),
            ),
            'password' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['password'],
                ),
            ),
            'refresh_token' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'flags' => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'regexp' => $regexp['refresh_token'],
                ),
            ),
        );

        return $definition;
    }

    public static function filter($query, $params = null)
    {
        $definition = self::initializer();

        $filtered_query = array_filter(filter_var_array($query, $definition));

        // Return entire result set, or only specific key(s).
        if ($params != null) {
            return array_intersect_key($filtered_query, array_flip($params));
        }
        return $filtered_query;
    }

    public static function checkResponseType(Request $request, Application $app)
    {
        // response_type should NEVER come from POST.
        if ($request->request->get('response_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set response_type.
        $query = array('response_type' => $request->query->get('response_type'));
        $filtered_query = self::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }
        $response_type = $request->query->get('response_type');

        // Check if given response_type supported.
        if (!isset($app['oauth2.response_type.' . $response_type])) {
            throw new UnsupportedResponseTypeException();
        }

        return $response_type;
    }

    public static function checkGrantType(Request $request, Application $app)
    {
        // grant_type should NEVER come from GET.
        if ($request->query->get('grant_type')) {
            throw new InvalidRequestException();
        }

        // Validate and set grant_type.
        $query = array('grant_type' => $request->request->get('grant_type'));
        $filtered_query = self::filter($query);
        if ($filtered_query != $query) {
            throw new InvalidRequestException();
        }
        $grant_type = $request->request->get('grant_type');

        // Check if given response_type supported.
        if (!isset($app['oauth2.grant_type.' . $grant_type])) {
            throw new UnsupportedGrantTypeException();
        }

        return $grant_type;
    }

    public static function checkClientId(Request $request, EntityRepository $repo)
    {
        // Fetch client_id from HTTP basic auth, POST, or GET.
        $client_id = null;
        if ($request->query->get('client_id')) {
            $client_id = $request->query->get('client_id');
        } elseif ($request->getUser()) {
            $client_id = $request->getUser();
        } elseif ($request->request->get('client_id')) {
            $client_id = $request->request->get('client_id');
        }

        // Check client_id with database record.
        $result = $repo->findClientByClientId($client_id);
        if ($result === null) {
            throw new InvalidClientException();
        }
        return $result->getClientId();
    }

    public static function checkScope(Request $request, EntityRepository $repo)
    {
        // Fetch scope from POST, or GET.
        $scope = array();
        if ($request->request->get('scope')) {
            $scope = $request->request->get('scope');
        } elseif ($request->query->get('scope')) {
            $scope = $request->query->get('scope');
        }

        if ($scope) {
            // Compare if given scope within all available stored scopes.
            $stored = array();
            $result = $repo->findScopes();
            foreach ($result as $row) {
                $stored[] = $row->getScope();
            }

            $scopes = preg_split('/\s+/', $scope);
            if (array_intersect($scopes, $stored) !== $scopes) {
                throw new InvalidScopeException();
            }

            return $scopes;
        }

        return false;
    }

    public static function checkScopeByCode(Request $request, EntityRepository $repo)
    {
        $code = $request->request->get('code');
        $client_id = $request->getUser() ? $request->getUser() : $request->request->get('client_id');

        // Fetch scope from pre-generated code.
        if ($result = $repo->findCodeByCode($code)) {
            if ($result !== null && $result->getClientId() === $client_id) {
                return $result->getScope();
            }
        }

        return false;
    }

    public static function checkScopeByRefreshToken(Request $request, EntityRepository $repo)
    {
        $refresh_token = $request->request->get('refresh_token');
        $client_id = $request->getUser() ? $request->getUser() : $request->request->get('client_id');

        // Fetch scope from pre-grnerated refresh_token.
        $stored = null;
        $result = $repo->findRefreshTokenByRefreshToken($refresh_token);
        if ($result !== null && $result->getClientId() == $client_id && $result->getScope()) {
            $stored = $result->getScope();
        }

        // Compare if given scope is subset of original refresh_token's scope.
        if ($request->request->get('scope') && $stored !== null) {
            $scopes = preg_split('/\s+/', $request->request->get('scope'));
            if (array_intersect($scopes, $stored) != $scopes) {
                throw new InvalidScopeException();
            }

            return $scopes;
        }
        // Return original refresh_token's scope if not specify in new request.
        elseif ($stored !== null) {
            return $stored;
        }

        return false;
    }

    public static function checkRedirectUri(Request $request, EntityRepository $repo)
    {
        // Getch redirect_uri from POST, or GET.
        $redirect_uri = null;
        $client_id = null;
        if ($request->request->get('redirect_uri') || $request->request->get('client_id')) {
            $redirect_uri = $request->request->get('redirect_uri');
            $client_id = $request->getUser() ? $request->getUser() : $request->request->get('client_id');
        } elseif ($request->query->get('redirect_uri') || $request->query->get('client_id')) {
            $redirect_uri = $request->query->get('redirect_uri');
            $client_id = $request->query->get('client_id');
        }

        // redirect_uri is not required if already established via other channels,
        // check an existing redirect URI against the one supplied.
        $stored = null;
        $result = $repo->findClientByClientId($client_id);
        if ($result !== null && $result->getRedirectUri()) {
            $stored = $result->getRedirectUri();
        }

        // At least one of: existing redirect URI or input redirect URI must be
        // specified.
        if (!$stored && !$redirect_uri) {
            throw new InvalidRequestException();
        }

        // If there's an existing uri and one from input, verify that they match.
        if ($stored && $redirect_uri) {
            // Ensure that the input uri starts with the stored uri.
            if (strcasecmp(substr($redirect_uri, 0, strlen($stored)), $stored) !== 0) {
                throw new InvalidRequestException();
            }
        }

        return $redirect_uri ? $redirect_uri : $stored;
    }

    public static function checkCode(Request $request, EntityRepository $repo)
    {
        // code is required
        if (!$request->request->get('code')) {
            throw new InvalidRequestException();
        }

        $code = $request->request->get('code');
        $client_id = $request->getUser() ? $request->getUser() : $request->request->get('client_id');

        // Check code with database record.
        $result = $repo->findCodeByCode($code);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidGrantException();
        }

        return $code;
    }

    public static function checkUsername(Request $request, EntityRepository $repo)
    {
        // username is required
        if (!$request->request->get('username')) {
            throw new InvalidRequestException();
        }

        $username = $request->request->get('username');

        // Check username with database record.
        return $repo->loadUserByUsername($username)->getUsername();
    }

    public static function checkPassword(Request $request, UserProviderInterface $provider, EncoderFactoryInterface $encoderFactory)
    {
        // password is required
        if (!$request->request->get('password')) {
            throw new InvalidRequestException();
        }

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // Fetch username with database record, we already confirm it is
        // valid with checkUsername().
        $result = $provider->loadUserByUsername($username);

        $encoder = $encoderFactory->getEncoder($result);
        if ($encoder->encodePassword($password, $result->getSalt()) !== $result->getPassword()) {
            throw new InvalidGrantException();
        }

        return $password;
    }


    public static function checkRefreshToken(Request $request, EntityRepository $repo)
    {
        // refresh_token is required
        if (!$request->request->get('refresh_token')) {
            throw new InvalidRequestException();
        }

        $refresh_token = $request->request->get('refresh_token');
        $client_id = $request->getUser() ? $request->getUser() : $request->request->get('client_id');

        // Check refresh_token with database record.
        $result = $repo->findRefreshTokenByRefreshToken($refresh_token);
        if ($result === null || $result->getClientId() !== $client_id) {
            throw new InvalidGrantException();
        } elseif ($result->getExpires() < time()) {
            throw new InvalidRequestException();
        }

        return $refresh_token;
    }
}
