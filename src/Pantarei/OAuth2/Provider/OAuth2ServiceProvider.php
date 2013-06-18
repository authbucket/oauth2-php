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
use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantTypeHandler;
use Pantarei\OAuth2\GrantType\ClientCredentialsGrantTypeHandler;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\GrantType\PasswordGrantTypeHandler;
use Pantarei\OAuth2\GrantType\RefreshTokenGrantTypeHandler;
use Pantarei\OAuth2\Model\ModelManagerFactory;
use Pantarei\OAuth2\ResponseType\CodeResponseTypeHandler;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\OAuth2\ResponseType\TokenResponseTypeHandler;
use Pantarei\OAuth2\Security\Authentication\Provider\ResourceProvider;
use Pantarei\OAuth2\Security\Authentication\Provider\TokenProvider;
use Pantarei\OAuth2\Security\Firewall\ResourceListener;
use Pantarei\OAuth2\Security\Firewall\TokenListener;
use Pantarei\OAuth2\TokenType\BearerTokenTypeHandler;
use Pantarei\OAuth2\TokenType\MacTokenTypeHandler;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        // Define backend storage manager before execute with addModelManager().
        $app['security.oauth2.model_manager.factory'] = $app->share(function () {
            return new ModelManagerFactory();
        });

        // Define response type handler before execute with addResponseTypeHandler().
        $app['security.oauth2.response_handler.factory'] = $app->share(function ($app) {
            return new ResponseTypeHandlerFactory();
        });

        // Define grant type handler before execute with addGrantTypeHandler().
        $app['security.oauth2.grant_handler.factory'] = $app->share(function ($app) {
            return new GrantTypeHandlerFactory();
        });

        // Default to bearer token for all request.
        $app['security.oauth2.token_handler.factory'] = $app->share(function ($app){
            return new TokenTypeHandlerFactory();
        });

        // Response type handler shared services.
        $app['security.oauth2.response_handler.code'] = $app->share(function () {
            return new CodeResponseTypeHandler();
        });
        $app['security.oauth2.response_handler.token'] = $app->share(function () {
            return new TokenResponseTypeHandler();
        });

        // Grant type handler shared services.
        $app['security.oauth2.grant_handler.authorization_code'] = $app->share(function () {
            return new AuthorizationCodeGrantTypeHandler();
        });
        $app['security.oauth2.grant_handler.client_credentials'] = $app->share(function () {
            return new ClientCredentialsGrantTypeHandler();
        });
        $app['security.oauth2.grant_handler.password'] = $app->share(function ($app) {
            // Symfony specific implementation, 3rd party integration should
            // override this setup with its own user credentials handling.
            $authenticationProvider = new DaoAuthenticationProvider(
                $app['security.oauth2.model_manager.factory']->getModelManager('user'),
                $app['security.user_checker'],
                'oauth2',
                $app['security.encoder_factory']
            );
            return new PasswordGrantTypeHandler($authenticationProvider);
        });
        $app['security.oauth2.grant_handler.refresh_token'] = $app->share(function () {
            return new RefreshTokenGrantTypeHandler();
        });

        // Token type handler shared services.
        $app['security.oauth2.token_handler.bearer'] = $app->share(function () {
            return new BearerTokenTypeHandler();
        });
        $app['security.oauth2.token_handler.mac'] = $app->share(function () {
            return new MacTokenTypeHandler();
        });

        $app['security.oauth2.authorize_controller'] = $app->protect(function ($request, $app) {
            $handler = new AuthorizeController(
                $app['security'],
                $app['security.oauth2.model_manager.factory'],
                $app['security.oauth2.response_handler.factory'],
                $app['security.oauth2.grant_handler.factory'],
                $app['security.oauth2.token_handler.factory']
            );
            return $handler->handle($request);
        });

        $app['security.oauth2.token_controller'] = $app->protect(function ($request, $app) {
            $handler = new TokenController(
                $app['security'],
                $app['security.oauth2.model_manager.factory'],
                $app['security.oauth2.response_handler.factory'],
                $app['security.oauth2.grant_handler.factory'],
                $app['security.oauth2.token_handler.factory']
            );
            return $handler->handle($request);
        });

        $app['security.authentication_provider.token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenProvider(
                    $app['security.oauth2.model_manager.factory']->getModelManager('client')
                );
            });
        });

        $app['security.authentication_listener.token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.token_handler.factory']
                );
            });
        });

        $app['security.authentication_provider.resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceProvider(
                    $app['security.oauth2.model_manager.factory']->getModelManager('access_token')
                );
            });
        });

        $app['security.authentication_listener.resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.token_handler.factory']
                );
            });
        });

        $app['security.authentication_listener.factory.token'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.' . $name . '.token'])) {
                $app['security.authentication_provider.' . $name . '.token'] = $app['security.authentication_provider.token._proto']($name, $options);
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
                $app['security.authentication_provider.' . $name . '.resource'] = $app['security.authentication_provider.resource._proto']($name, $options);
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
