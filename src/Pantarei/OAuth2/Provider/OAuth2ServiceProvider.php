<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Provider;

use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\Security\Authentication\Provider\OAuth2Provider;
use Pantarei\OAuth2\Security\Firewall\OAuth2Listener;
use Pantarei\OAuth2\Security\GrantType\AuthorizationCodeGrantTypeHandler;
use Pantarei\OAuth2\Security\GrantType\ClientCredentialsGrantTypeHandler;
use Pantarei\OAuth2\Security\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\Security\GrantType\PasswordGrantTypeHandler;
use Pantarei\OAuth2\Security\GrantType\RefreshTokenGrantTypeHandler;
use Pantarei\OAuth2\Security\ResponseType\CodeResponseTypeHandler;
use Pantarei\OAuth2\Security\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\OAuth2\Security\ResponseType\TokenResponseTypeHandler;
use Pantarei\OAuth2\Security\TokenType\BearerTokenTypeHandler;
use Pantarei\OAuth2\Security\TokenType\MacTokenTypeHandler;
use Pantarei\OAuth2\Security\TokenType\TokenTypeHandlerFactory;
use Pantarei\Oauth2\Model\ModelManagerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider implements ServiceProviderInterface
{
    protected $fakeRoutes;

    public function register(Application $app)
    {
        // Used to register routes for authorize_path and token_path.
        $this->fakeRoutes = array();

        $that = $this;

        // Before execute we need to define the backend storage.
        $app['security.oauth2.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory();
        });

        $app['security.oauth2.response_type_handler.factory'] = $app->share(function ($app) {
            return new ResponseTypeHandlerFactory(array(
                'code' => new CodeResponseTypeHandler(),
                'token' => new TokenResponseTypeHandler(),
            ));
        });

        $app['security.oauth2.grant_type_handler.factory'] = $app->share(function ($app) {
            return new GrantTypeHandlerFactory(array(
                'authorization_code' => new AuthorizationCodeGrantTypeHandler(),
                'client_credentials' => new ClientCredentialsGrantTypeHandler(),
                'password' => new PasswordGrantTypeHandler(),
                'refresh_token' => new RefreshTokenGrantTypeHandler(),
            ));
        });

        // Default to bearer token for all request.
        $app['security.oauth2.token_type_handler.factory'] = $app->share(function ($app){
            return new TokenTypeHandlerFactory(array(
                'bearer' => new BearerTokenTypeHandler(),
            ));
        });

        $app['security.authentication_listener.factory.oauth2'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_listener.' . $name . '.oauth2'])) {
                $app['security.authentication_listener.' . $name . '.oauth2'] = $app['security.authentication_listener.oauth2._proto']($name, $options);
            }

            if (!isset($app['security.authentication_provider.' . $name . '.oauth2'])) {
                $app['security.authentication_provider.' . $name . '.oauth2'] = $app['security.authentication_provider.dao._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.oauth2',
                'security.authentication_listener.' . $name . '.oauth2',
                null,
                'pre_auth',
            );
        });

        $app['security.authentication_listener.oauth2._proto'] = $app->protect(function ($name, $options) use ($app, $that) {
            return $app->share(function () use ($app, $name, $options, $that) {
                $that->addFakeRoute(
                    'get',
                    $tmp = isset($options['authorize_path']) ? $options['authorize_path'] : '/authorize',
                    str_replace('/', '_', ltrim($tmp, '/'))
                );

                $that->addFakeRoute(
                    'post',
                    $tmp = isset($options['token_path']) ? $options['token_path'] : '/token',
                    str_replace('/', '_', ltrim($tmp, '/'))
                );

                return new OAuth2Listener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $app['security.http_utils'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.response_type_handler.factory'],
                    $app['security.oauth2.grant_type_handler.factory'],
                    $app['security.oauth2.token_type_handler.factory'],
                    $name,
                    $options
                );
            });
        });

        // Shortcut for response_type.
        $response_type = array(
            'code' => 'Pantarei\OAuth2\ResponseType\CodeResponseType',
            'token' => 'Pantarei\OAuth2\ResponseType\TokenResponseType',
        );
        foreach ($response_type as $name => $class) {
            $app['oauth2.response_type.' . $name] = $class;
        }

        // Shortcut for token_type.
        $token_type = array(
            'bearer' => 'Pantarei\OAuth2\TokenType\BearerTokenType',
            'mac' => 'Pantarei\OAuth2\TokenType\MacTokenType',
        );
        foreach ($token_type as $name => $class) {
            $app['oauth2.token_type.' . $name] = $class;
        }

        // Shortcut for default token_type.
        $app['oauth2.token_type.default'] = $app['oauth2.token_type.bearer'];
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addListener('kernel.request', array($app['security.firewall'], 'onKernelRequest'), 8);

        foreach ($this->fakeRoutes as $route) {
            list($method, $pattern, $name) = $route;

            $app->$method($pattern, function() {})->bind($name);
        }
    }

    public function addFakeRoute($method, $pattern, $name)
    {
        $this->fakeRoutes[] = array($method, $pattern, $name);
    }
}
