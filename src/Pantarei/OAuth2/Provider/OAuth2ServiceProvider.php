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
use Pantarei\OAuth2\Controller\ResourceController;
use Pantarei\OAuth2\Controller\TokenController;
use Pantarei\OAuth2\EventListener\ExceptionListener;
use Pantarei\OAuth2\Exception\ServerErrorException;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\OAuth2\Security\Authentication\Provider\ResourceProvider;
use Pantarei\OAuth2\Security\Authentication\Provider\TokenProvider;
use Pantarei\OAuth2\Security\Firewall\ResourceListener;
use Pantarei\OAuth2\Security\Firewall\TokenListener;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        // Add default response type handler.
        if (!isset($app['oauth2.response_handler'])) {
            $app['oauth2.response_handler'] = array(
                'code' => 'Pantarei\\OAuth2\\ResponseType\\CodeResponseTypeHandler',
                'token' => 'Pantarei\\OAuth2\\ResponseType\\TokenResponseTypeHandler',
            );
        }

        // Add default grant type handler.
        if (!isset($app['oauth2.grant_handler'])) {
            $app['oauth2.grant_handler'] = array(
                'authorization_code' => 'Pantarei\\OAuth2\\GrantType\\AuthorizationCodeGrantTypeHandler',
                'client_credentials' => 'Pantarei\\OAuth2\\GrantType\\ClientCredentialsGrantTypeHandler',
                'password' => 'Pantarei\\OAuth2\\GrantType\\PasswordGrantTypeHandler',
                'refresh_token' => 'Pantarei\\OAuth2\\GrantType\\RefreshTokenGrantTypeHandler',
            );
        }

        // Add default token type handler.
        if (!isset($app['oauth2.token_handler'])) {
            $app['oauth2.token_handler'] = array(
                'bearer' => 'Pantarei\\OAuth2\\TokenType\\BearerTokenTypeHandler',
                'mac' => 'Pantarei\\OAuth2\\TokenType\\MacTokenTypeHandler',
            );
        }

        $app['oauth2.exception_listener'] = $app->share(function () {
            return new ExceptionListener();
        });

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

        $app['oauth2.resource_controller'] = $app->share(function () use ($app) {
            return new ResourceController(
                $app['security']
            );
        });

        $app['security.authentication_provider.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenProvider(
                    $app['oauth2.model_manager.factory'],
                    $name
                );
            });
        });

        $app['security.authentication_listener.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $name
                );
            });
        });

        $app['security.authentication_provider.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceProvider(
                    $app['oauth2.model_manager.factory'],
                    $name
                );
            });
        });

        $app['security.authentication_listener.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $name,
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
        $app['dispatcher']->addListener(KernelEvents::EXCEPTION, array($app['oauth2.exception_listener'], 'onKernelException'), -8);
    }
}
