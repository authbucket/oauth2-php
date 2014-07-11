<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Provider;

use AuthBucket\OAuth2\Controller\AuthorizeController;
use AuthBucket\OAuth2\Controller\DebugController;
use AuthBucket\OAuth2\Controller\TokenController;
use AuthBucket\OAuth2\EventListener\ExceptionListener;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactory;
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use AuthBucket\OAuth2\Security\Authentication\Provider\ResourceProvider;
use AuthBucket\OAuth2\Security\Authentication\Provider\TokenProvider;
use AuthBucket\OAuth2\Security\Firewall\ResourceListener;
use AuthBucket\OAuth2\Security\Firewall\TokenListener;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthBucketOAuth2ServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        // Add default response type handler.
        if (!isset($app['authbucket_oauth2.response_handler'])) {
            $app['authbucket_oauth2.response_handler'] = array(
                'code' => 'AuthBucket\\OAuth2\\ResponseType\\CodeResponseTypeHandler',
                'token' => 'AuthBucket\\OAuth2\\ResponseType\\TokenResponseTypeHandler',
            );
        }

        // Add default grant type handler.
        if (!isset($app['authbucket_oauth2.grant_handler'])) {
            $app['authbucket_oauth2.grant_handler'] = array(
                'authorization_code' => 'AuthBucket\\OAuth2\\GrantType\\AuthorizationCodeGrantTypeHandler',
                'client_credentials' => 'AuthBucket\\OAuth2\\GrantType\\ClientCredentialsGrantTypeHandler',
                'password' => 'AuthBucket\\OAuth2\\GrantType\\PasswordGrantTypeHandler',
                'refresh_token' => 'AuthBucket\\OAuth2\\GrantType\\RefreshTokenGrantTypeHandler',
            );
        }

        // Add default token type handler.
        if (!isset($app['authbucket_oauth2.token_handler'])) {
            $app['authbucket_oauth2.token_handler'] = array(
                'bearer' => 'AuthBucket\\OAuth2\\TokenType\\BearerTokenTypeHandler',
                'mac' => 'AuthBucket\\OAuth2\\TokenType\\MacTokenTypeHandler',
            );
        }

        $app['authbucket_oauth2.exception_listener'] = $app->share(function () {
            return new ExceptionListener();
        });

        // Override this with your backend model managers, e.g. Doctrine ORM
        // EntityRepository.
        $app['authbucket_oauth2.model_manager.factory'] = $app->share(function () {
            throw new ServerErrorException();
        });

        $app['authbucket_oauth2.response_handler.factory'] = $app->share(function ($app) {
            return new ResponseTypeHandlerFactory($app['authbucket_oauth2.response_handler']);
        });

        $app['authbucket_oauth2.grant_handler.factory'] = $app->share(function ($app) {
            return new GrantTypeHandlerFactory($app['authbucket_oauth2.grant_handler']);
        });

        $app['authbucket_oauth2.token_handler.factory'] = $app->share(function ($app) {
            return new TokenTypeHandlerFactory($app['authbucket_oauth2.token_handler']);
        });

        // For sending user to scope authorize page due to insufficient scope,
        // override the last parameter with redirect URI, e.g.
        // '/oauth2/authorize/scope'.
        $app['authbucket_oauth2.authorize_controller'] = $app->share(function () use ($app) {
            return new AuthorizeController(
                $app['security'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.response_handler.factory'],
                $app['authbucket_oauth2.token_handler.factory'],
                null
            );
        });

        // For using grant_type = password, override the last parameter
        // with your own user provider, e.g. using InMemoryUserProvider or
        // a doctrine EntityRepository that implements UserProviderInterface.
        $app['authbucket_oauth2.token_controller'] = $app->share(function () use ($app) {
            return new TokenController(
                $app['security'],
                $app['security.user_checker'],
                $app['security.encoder_factory'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.grant_handler.factory'],
                $app['authbucket_oauth2.token_handler.factory'],
                null
            );
        });

        $app['authbucket_oauth2.debug_controller'] = $app->share(function () use ($app) {
            return new DebugController(
                $app['security'],
                $app['authbucket_oauth2.model_manager.factory']
            );
        });

        $app['security.authentication_provider.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenProvider(
                    $app['authbucket_oauth2.model_manager.factory'],
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
                $options = array_merge(array(
                    'resource_type' => 'model',
                    'scope' => array(),
                    'options' => array(),
                ), (array) $options);

                return new ResourceProvider(
                    $app['authbucket_oauth2.model_manager.factory'],
                    $name,
                    $options['resource_type'],
                    $options['scope'],
                    $options['options']
                );
            });
        });

        $app['security.authentication_listener.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $app['security'],
                    $app['security.authentication_manager'],
                    $name,
                    $app['authbucket_oauth2.token_handler.factory']
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
        $app['dispatcher']->addListener(KernelEvents::EXCEPTION, array($app['authbucket_oauth2.exception_listener'], 'onKernelException'), -8);
    }
}
