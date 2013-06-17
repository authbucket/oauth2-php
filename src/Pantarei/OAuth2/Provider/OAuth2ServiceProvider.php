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

use Pantarei\OAuth2\Controller\AuthorizeController;
use Pantarei\OAuth2\Controller\TokenController;
use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantTypeHandler;
use Pantarei\OAuth2\GrantType\ClientCredentialsGrantTypeHandler;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\GrantType\PasswordGrantTypeHandler;
use Pantarei\OAuth2\GrantType\RefreshTokenGrantTypeHandler;
use Pantarei\OAuth2\Model\ModelManagerFactory;
use Pantarei\OAuth2\ResponseType\CodeResponseTypeHandler;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\OAuth2\ResponseType\TokenResponseTypeHandler;
use Pantarei\OAuth2\Security\Firewall\ResourceListener;
use Pantarei\OAuth2\Security\Firewall\TokenListener;
use Pantarei\OAuth2\TokenType\BearerTokenTypeHandler;
use Pantarei\OAuth2\TokenType\MacTokenTypeHandler;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        // Before execute we need to define the backend storage with addModelManager().
        $app['security.oauth2.model_manager.factory'] = $app->share(function () {
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

        $app['security.oauth2.authorize_controller'] = $app->protect(function ($request, $app) {
            $handler = new AuthorizeController(
                $app['security'],
                $app['security.authentication_manager'],
                $app['security.oauth2.model_manager.factory'],
                $app['security.oauth2.response_type_handler.factory'],
                $app['security.oauth2.grant_type_handler.factory'],
                $app['security.oauth2.token_type_handler.factory'],
                'authorize'
            );
            return $handler->handle($request);
        });

        $app['security.oauth2.token_controller'] = $app->protect(function ($request, $app) {
            $handler = new TokenController(
                $app['security'],
                $app['security.authentication_manager'],
                $app['security.oauth2.model_manager.factory'],
                $app['security.oauth2.response_type_handler.factory'],
                $app['security.oauth2.grant_type_handler.factory'],
                $app['security.oauth2.token_type_handler.factory'],
                'token'
            );
            return $handler->handle($request);
        });

        $app['security.authentication_listener.token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenListener(
                    $app['security'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.token_type_handler.factory']
                );
            });
        });

        $app['security.authentication_listener.resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $app['security'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.token_type_handler.factory']
                );
            });
        });

        $app['security.authentication_listener.factory.token'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.' . $name . '.token'])) {
                $app['security.authentication_provider.' . $name . '.token'] = $app['security.authentication_provider.dao._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.' . $name . '.token'])) {
                $app['security.authentication_listener.' . $name . '.token'] = $app['security.authentication_listener.token._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.token',
                'security.authentication_listener.' . $name . '.token',
                null,
                'pre_auth',
            );
        });

        $app['security.authentication_listener.factory.resource'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.' . $name . '.resource'])) {
                $app['security.authentication_provider.' . $name . '.resource'] = $app['security.authentication_provider.dao._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.' . $name . '.resource'])) {
                $app['security.authentication_listener.' . $name . '.resource'] = $app['security.authentication_listener.resource._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.resource',
                'security.authentication_listener.' . $name . '.resource',
                null,
                'pre_auth',
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
