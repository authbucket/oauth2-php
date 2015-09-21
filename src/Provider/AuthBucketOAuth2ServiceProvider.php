<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Provider;

use AuthBucket\OAuth2\Controller\OAuth2Controller;
use AuthBucket\OAuth2\EventListener\ExceptionListener;
use AuthBucket\OAuth2\GrantType\GrantTypeHandlerFactory;
use AuthBucket\OAuth2\Model\InMemory\ModelManagerFactory;
use AuthBucket\OAuth2\ResourceType\ResourceTypeHandlerFactory;
use AuthBucket\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use AuthBucket\OAuth2\Security\Authentication\Provider\ResourceProvider;
use AuthBucket\OAuth2\Security\Authentication\Provider\TokenProvider;
use AuthBucket\OAuth2\Security\Firewall\ResourceListener;
use AuthBucket\OAuth2\Security\Firewall\TokenListener;
use AuthBucket\OAuth2\TokenType\TokenTypeHandlerFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * OAuth2 service provider as plugin for Silex SecurityServiceProvider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthBucketOAuth2ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // (Optional) Override this with your own model classes, default with
        // in-memory AccessToken for using resource firewall with remote debug
        // endpoint.
        $app['authbucket_oauth2.model'] = [
            'access_token' => 'AuthBucket\\OAuth2\\Model\\InMemory\\AccessToken',
        ];

        // (Optional) Override this with your backend model managers, e.g.
        // Doctrine ORM EntityRepository, default with in-memory
        // implementation for using resource firewall with remote debug
        // endpoint.
        $app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
            return new ModelManagerFactory($app['authbucket_oauth2.model']);
        });

        // (Optional) For using grant_type = password, override this parameter
        // with your own user provider, e.g. using InMemoryUserProvider or a
        // Doctrine ORM EntityRepository that implements UserProviderInterface.
        $app['authbucket_oauth2.user_provider'] = null;

        // Add default response type handler.
        $app['authbucket_oauth2.response_handler'] = [
            'code' => 'AuthBucket\\OAuth2\\ResponseType\\CodeResponseTypeHandler',
            'token' => 'AuthBucket\\OAuth2\\ResponseType\\TokenResponseTypeHandler',
        ];

        // Add default grant type handler.
        $app['authbucket_oauth2.grant_handler'] = [
            'authorization_code' => 'AuthBucket\\OAuth2\\GrantType\\AuthorizationCodeGrantTypeHandler',
            'client_credentials' => 'AuthBucket\\OAuth2\\GrantType\\ClientCredentialsGrantTypeHandler',
            'password' => 'AuthBucket\\OAuth2\\GrantType\\PasswordGrantTypeHandler',
            'refresh_token' => 'AuthBucket\\OAuth2\\GrantType\\RefreshTokenGrantTypeHandler',
        ];

        // Add default token type handler.
        $app['authbucket_oauth2.token_handler'] = [
            'bearer' => 'AuthBucket\\OAuth2\\TokenType\\BearerTokenTypeHandler',
            'mac' => 'AuthBucket\\OAuth2\\TokenType\\MacTokenTypeHandler',
        ];

        // Add default resource type handler.
        $app['authbucket_oauth2.resource_handler'] = [
            'model' => 'AuthBucket\\OAuth2\\ResourceType\\ModelResourceTypeHandler',
            'debug_endpoint' => 'AuthBucket\\OAuth2\\ResourceType\\DebugEndpointResourceTypeHandler',
        ];

        $app['authbucket_oauth2.exception_listener'] = $app->share(function ($app) {
            return new ExceptionListener(
                $app['logger']
            );
        });

        $app['authbucket_oauth2.response_handler.factory'] = $app->share(function ($app) {
            return new ResponseTypeHandlerFactory(
                $app['security.token_storage'],
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_handler.factory'],
                $app['authbucket_oauth2.response_handler']
            );
        });

        $app['authbucket_oauth2.grant_handler.factory'] = $app->share(function ($app) {
            return new GrantTypeHandlerFactory(
                $app['security.token_storage'],
                $app['security.user_checker'],
                $app['security.encoder_factory'],
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_handler.factory'],
                $app['authbucket_oauth2.user_provider'],
                $app['authbucket_oauth2.grant_handler']
            );
        });

        $app['authbucket_oauth2.token_handler.factory'] = $app->share(function ($app) {
            return new TokenTypeHandlerFactory(
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.token_handler']
            );
        });

        $app['authbucket_oauth2.resource_handler.factory'] = $app->share(function ($app) {
            return new ResourceTypeHandlerFactory(
                $app,
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.resource_handler']
            );
        });

        $app['authbucket_oauth2.oauth2_controller'] = $app->share(function () use ($app) {
            return new OAuth2Controller(
                $app['security.token_storage'],
                $app['validator'],
                $app['authbucket_oauth2.model_manager.factory'],
                $app['authbucket_oauth2.response_handler.factory'],
                $app['authbucket_oauth2.grant_handler.factory']
            );
        });

        $app['security.authentication_provider.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenProvider(
                    $name,
                    $app['authbucket_oauth2.model_manager.factory']
                );
            });
        });

        $app['security.authentication_listener.oauth2_token._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new TokenListener(
                    $name,
                    $app['security.token_storage'],
                    $app['security.authentication_manager'],
                    $app['validator'],
                    $app['logger']
                );
            });
        });

        $app['security.authentication_provider.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceProvider(
                    $name,
                    $app['authbucket_oauth2.resource_handler.factory'],
                    $options['resource_type'],
                    $options['scope'],
                    $options['options']
                );
            });
        });

        $app['security.authentication_listener.oauth2_resource._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new ResourceListener(
                    $name,
                    $app['security.token_storage'],
                    $app['security.authentication_manager'],
                    $app['validator'],
                    $app['logger'],
                    $app['authbucket_oauth2.token_handler.factory']
                );
            });
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
                null,
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
                null,
                'pre_auth',
            ];
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['authbucket_oauth2.exception_listener']);
    }
}
