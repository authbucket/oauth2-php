<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Provider;

use Pantarei\Oauth2\Controller\AuthorizeController;
use Pantarei\Oauth2\Controller\TokenController;
use Pantarei\Oauth2\Exception\ServerErrorException;
use Pantarei\Oauth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\Oauth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\Oauth2\Security\Authentication\Provider\ResourceProvider;
use Pantarei\Oauth2\Security\Authentication\Provider\TokenProvider;
use Pantarei\Oauth2\Security\Firewall\ResourceListener;
use Pantarei\Oauth2\Security\Firewall\TokenListener;
use Pantarei\Oauth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Oauth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class Oauth2ServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        // Add default response type handler.
        if (!isset($app['oauth2.response_handler'])) {
            $app['oauth2.response_handler'] = array(
                'code' => 'Pantarei\\Oauth2\\ResponseType\\CodeResponseTypeHandler',
                'token' => 'Pantarei\\Oauth2\\ResponseType\\TokenResponseTypeHandler',
            );
        }

        // Add default grant type handler.
        if (!isset($app['oauth2.grant_handler'])) {
            $app['oauth2.grant_handler'] = array(
                'authorization_code' => 'Pantarei\\Oauth2\\GrantType\\AuthorizationCodeGrantTypeHandler',
                'client_credentials' => 'Pantarei\\Oauth2\\GrantType\\ClientCredentialsGrantTypeHandler',
                'password' => 'Pantarei\\Oauth2\\GrantType\\PasswordGrantTypeHandler',
                'refresh_token' => 'Pantarei\\Oauth2\\GrantType\\RefreshTokenGrantTypeHandler',
            );
        }

        // Add default token type handler.
        if (!isset($app['oauth2.token_handler'])) {
            $app['oauth2.token_handler'] = array(
                'bearer' => 'Pantarei\\Oauth2\\TokenType\\BearerTokenTypeHandler',
                'mac' => 'Pantarei\\Oauth2\\TokenType\\MacTokenTypeHandler',
            );
        }

        // Override this with your backend model managers, e.g. Doctrine ORM
        // EntityRepository.
        $app['oauth2.model_manager.factory'] = $app->share(function () {
            throw new ServerErrorException();
        });

        $app['oauth2.response_handler.factory'] = $app->share(function ($app) {
            return new ResponseTypeHandlerFactory($app['oauth2.response_handler']);
        });

        $app['oauth2.grant_handler.factory'] = $app->share(function ($app) {
            return new GrantTypeHandlerFactory($app['oauth2.grant_handler']);
        });

        $app['oauth2.token_handler.factory'] = $app->share(function ($app){
            return new TokenTypeHandlerFactory($app['oauth2.token_handler']);
        });

        $app['oauth2.authorize_controller'] = $app->share(function () use ($app) {
            return new AuthorizeController(
                $app['security'],
                $app['oauth2.model_manager.factory'],
                $app['oauth2.response_handler.factory'],
                $app['oauth2.token_handler.factory']
            );
        });

        // For using grant_type = password, override the last parameter
        // with your own user provider, e.g. using InMemoryUserProvider or
        // a doctrine EntityRepository that implements UserProviderInterface.
        $app['oauth2.token_controller'] = $app->share(function () use ($app) {
            return new TokenController(
                $app['security'],
                $app['security.user_checker'],
                $app['security.encoder_factory'],
                $app['oauth2.model_manager.factory'],
                $app['oauth2.grant_handler.factory'],
                $app['oauth2.token_handler.factory'],
                null
            );
        });

        $app['security.authentication_provider.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenProvider(
                    $app['oauth2.model_manager.factory']
                );
            });
        });

        $app['security.authentication_listener.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenListener(
                    $app['security'],
                    $app['security.authentication_manager']
                );
            });
        });

        $app['security.authentication_provider.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceProvider(
                    $app['oauth2.model_manager.factory']
                );
            });
        });

        $app['security.authentication_listener.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $app['oauth2.token_handler.factory']
                );
            });
        });

        $app['security.authentication_listener.factory.oauth2_token'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.' . $name . '.oauth2_token'])) {
                $app['security.authentication_provider.' . $name . '.oauth2_token'] = $app['security.authentication_provider.oauth2_token._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.' . $name . '.oauth2_token'])) {
                $app['security.authentication_listener.' . $name . '.oauth2_token'] = $app['security.authentication_listener.oauth2_token._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.oauth2_token',
                'security.authentication_listener.' . $name . '.oauth2_token',
                null,
                'pre_auth',
            );
        });

        $app['security.authentication_listener.factory.oauth2_resource'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.' . $name . '.oauth2_resource'])) {
                $app['security.authentication_provider.' . $name . '.oauth2_resource'] = $app['security.authentication_provider.oauth2_resource._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.' . $name . '.oauth2_resource'])) {
                $app['security.authentication_listener.' . $name . '.oauth2_resource'] = $app['security.authentication_listener.oauth2_resource._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.oauth2_resource',
                'security.authentication_listener.' . $name . '.oauth2_resource',
                null,
                'pre_auth',
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
