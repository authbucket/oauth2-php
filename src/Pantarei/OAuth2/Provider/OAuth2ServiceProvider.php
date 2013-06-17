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
use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantTypeHandler;
use Pantarei\OAuth2\GrantType\ClientCredentialsGrantTypeHandler;
use Pantarei\OAuth2\GrantType\GrantTypeHandlerFactory;
use Pantarei\OAuth2\GrantType\PasswordGrantTypeHandler;
use Pantarei\OAuth2\GrantType\RefreshTokenGrantTypeHandler;
use Pantarei\OAuth2\ResponseType\CodeResponseTypeHandler;
use Pantarei\OAuth2\ResponseType\ResponseTypeHandlerFactory;
use Pantarei\OAuth2\ResponseType\TokenResponseTypeHandler;
use Pantarei\OAuth2\Security\Firewall\OAuth2Listener;
use Pantarei\OAuth2\TokenType\BearerTokenTypeHandler;
use Pantarei\OAuth2\TokenType\MacTokenTypeHandler;
use Pantarei\OAuth2\TokenType\TokenTypeHandlerFactory;
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

    public function register(Application $app)
    {
        // Before execute we need to define the backend storage.
        $app['security.oauth2.model_manager.factory'] = $app->share(function ($app) {
            throw new ServerErrorException();
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
            if (!isset($app['security.authentication_provider.' . $name . '.oauth2'])) {
                $app['security.authentication_provider.' . $name . '.oauth2'] = $app['security.authentication_provider.dao._proto']($name, $options);
            }

            if (!isset($app['security.authentication_listener.' . $name . '.oauth2'])) {
                $app['security.authentication_listener.' . $name . '.oauth2'] = $app['security.authentication_listener.oauth2._proto']($name, $options);
            }

            return array(
                'security.authentication_provider.' . $name . '.oauth2',
                'security.authentication_listener.' . $name . '.oauth2',
                null,
                'pre_auth',
            );
        });

        $app['security.authentication_listener.oauth2._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                return new OAuth2Listener(
                    $app['security'],
                    $app['security.http_utils'],
                    $app['security.oauth2.model_manager.factory'],
                    $app['security.oauth2.token_type_handler.factory'],
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
    }
}
