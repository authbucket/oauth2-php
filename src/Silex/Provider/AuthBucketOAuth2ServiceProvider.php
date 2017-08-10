<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Silex\Provider;

use AuthBucket\OAuth2\Controller\AuthorizationController;
use AuthBucket\OAuth2\Controller\TokenController;
use AuthBucket\OAuth2\Controller\DebugController;
use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactory;
use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactory;
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use AuthBucket\OAuth2\Symfony\Component\EventDispatcher\ExceptionListener;
use AuthBucket\OAuth2\Symfony\Component\Security\Core\Authentication\Provider\ResourceProvider;
use AuthBucket\OAuth2\Symfony\Component\Security\Core\Authentication\Provider\TokenProvider;
use AuthBucket\OAuth2\Symfony\Component\Security\Http\EntryPoint\ResourceAuthenticationEntryPoint;
use AuthBucket\OAuth2\Symfony\Component\Security\Http\EntryPoint\TokenAuthenticationEntryPoint;
use AuthBucket\OAuth2\Symfony\Component\Security\Http\Firewall\ResourceListener;
use AuthBucket\OAuth2\Symfony\Component\Security\Http\Firewall\TokenListener;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthBucketOAuth2ServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        // (Optional) Override this with your own model classes, default with
        // in-memory AccessToken for using resource firewall with remote debug
        // endpoint.
        $app['authbucket_oauth2.model'] = [
            'access_token' => 'AuthBucket\\OAuth2\\Model\\AccessToken',
        ];

        // (Optional) Override this with your backend model managers, e.g.
        // Doctrine ORM EntityRepository, default with in-memory
        // implementation for using resource firewall with remote debug
        // endpoint.
        $app['authbucket_oauth2.model_manager.factory'] = $app->factory(function ($app) {
            return new ModelManagerFactory($app['authbucket_oauth2.model']);
        });

        // (Optional) For using grant_type = password, override this parameter
        // with your own user provider, e.g. using InMemoryUserProvider or a
        // Doctrine ORM EntityRepository that implements UserProviderInterface.
        $app['authbucket_oauth2.user_provider'] = null;

        // Add default response type handler.
        $app['authbucket_oauth2.response_type_handler'] = [
            'code' => 'AuthBucket\\OAuth2\\ResponseType\\CodeResponseTypeHandler',
            'token' => 'AuthBucket\\OAuth2\\ResponseType\\TokenResponseTypeHandler',
        ];

        // Add default grant type handler.
        $app['authbucket_oauth2.grant_type_handler'] = [
            'authorization_code' => 'AuthBucket\\OAuth2\\GrantType\\AuthorizationCodeGrantTypeHandler',
            'client_credentials' => 'AuthBucket\\OAuth2\\GrantType\\ClientCredentialsGrantTypeHandler',
            'password' => 'AuthBucket\\OAuth2\\GrantType\\PasswordGrantTypeHandler',
            'refresh_token' => 'AuthBucket\\OAuth2\\GrantType\\RefreshTokenGrantTypeHandler',
        ];

        // Add default token type handler.
        $app['authbucket_oauth2.token_type_handler'] = [
            'bearer' => 'AuthBucket\\OAuth2\\TokenType\\BearerTokenTypeHandler',
            'mac' => 'AuthBucket\\OAuth2\\TokenType\\MacTokenTypeHandler',
        ];

        // Add default resource type handler.
        $app['authbucket_oauth2.resource_type_handler'] = [
            'model' => 'AuthBucket\\OAuth2\\ResourceType\\ModelResourceTypeHandler',
            'debug_endpoint' => 'AuthBucket\\OAuth2\\ResourceType\\DebugEndpointResourceTypeHandler',
        ];

        $app['authbucket_oauth2.exception_listener'] = function ($app) {
            return new ExceptionListener(
                $app['logger']
            );
        };

        $app['authbucket_oauth2.response_type_handler.factory'] = $app->factory(function ($app) {
            return new ResponseTypeHandlerFactory(
                $app['security.token_storage'],
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_type_handler.factory'],
                $app['authbucket_oauth2.response_type_handler']
            );
        });

        $app['authbucket_oauth2.grant_type_handler.factory'] = $app->factory(function ($app) {
            return new GrantTypeHandlerFactory(
                $app['security.token_storage'],
                $app['security.encoder_factory'],
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_type_handler.factory'],
                $app['authbucket_oauth2.user_provider'],
                $app['authbucket_oauth2.grant_type_handler']
            );
        });

        $app['authbucket_oauth2.token_type_handler.factory'] = $app->factory(function ($app) {
            return new TokenTypeHandlerFactory(
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_type_handler']
            );
        });

        $app['authbucket_oauth2.resource_type_handler.factory'] = $app->factory(function ($app) {
            return new ResourceTypeHandlerFactory(
                $app,
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.resource_type_handler']
            );
        });

        $app['authbucket_oauth2.authorization_controller'] = function () use ($app) {
            return new AuthorizationController(
                $app['validator'],
                $app['authbucket_oauth2.response_type_handler.factory']
            );
        };

        $app['authbucket_oauth2.token_controller'] = function () use ($app) {
            return new TokenController(
                $app['validator'],
                $app['authbucket_oauth2.grant_type_handler.factory']
            );
        };

        $app['authbucket_oauth2.debug_controller'] = function () use ($app) {
            return new DebugController(
                $app['security.token_storage']
            );
        };

        $app['authbucket_oauth2.token_entry_point'] = function () {
            return new TokenAuthenticationEntryPoint();
        };

        $app['authbucket_oauth2.resource_entry_point'] = function () {
            return new ResourceAuthenticationEntryPoint();
        };

        $app['security.authentication_provider.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return function () use ($app, $name, $options) {
                return new TokenProvider(
                    $name,
                    $app['authbucket_oauth2.model_manager.factory']
                );
            };
        });

        $app['security.authentication_listener.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return function () use ($app, $name, $options) {
                return new TokenListener(
                    $name,
                    $app['security.token_storage'],
                    $app['security.authentication_manager'],
                    $app['validator'],
                    $app['logger']
                );
            };
        });

        $app['security.authentication_provider.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return function () use ($app, $name, $options) {
                return new ResourceProvider(
                    $name,
                    $app['authbucket_oauth2.resource_type_handler.factory'],
                    $options['resource_type'],
                    $options['scope'],
                    $options['options']
                );
            };
        });

        $app['security.authentication_listener.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return function () use ($app, $name, $options) {
                return new ResourceListener(
                    $name,
                    $app['security.token_storage'],
                    $app['security.authentication_manager'],
                    $app['validator'],
                    $app['logger'],
                    $app['authbucket_oauth2.token_type_handler.factory']
                );
            };
        });

        $app['security.authentication_listener.factory.oauth2_token'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_provider.'.$name.'.oauth2_token'])) {
                $app['security.authentication_provider.'.$name.'.oauth2_token'] = $app['security.authentication_provider.oauth2_token._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.'.$name.'.oauth2_token'])) {
                $app['security.authentication_listener.'.$name.'.oauth2_token'] = $app['security.authentication_listener.oauth2_token._proto']($name, $options);
            }

            return [
                'security.authentication_provider.'.$name.'.oauth2_token',
                'security.authentication_listener.'.$name.'.oauth2_token',
                'authbucket_oauth2.token_entry_point',
                'pre_auth',
            ];
        });

        $app['security.authentication_listener.factory.oauth2_resource'] = $app->protect(function ($name, $options) use ($app) {
            $options = array_merge([
                'resource_type' => 'model',
                'scope' => [],
                'options' => [],
            ], (array) $options);

            if (!isset($app['security.authentication_provider.'.$name.'.oauth2_resource'])) {
                $app['security.authentication_provider.'.$name.'.oauth2_resource'] = $app['security.authentication_provider.oauth2_resource._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.'.$name.'.oauth2_resource'])) {
                $app['security.authentication_listener.'.$name.'.oauth2_resource'] = $app['security.authentication_listener.oauth2_resource._proto']($name, $options);
            }

            return [
                'security.authentication_provider.'.$name.'.oauth2_resource',
                'security.authentication_listener.'.$name.'.oauth2_resource',
                'authbucket_oauth2.resource_entry_point',
                'pre_auth',
            ];
        });
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['authbucket_oauth2.exception_listener']);
    }

    public function boot(Application $app)
    {
    }
}
