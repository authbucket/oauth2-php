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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * A simple Doctrine ORM service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // OAuth2 default options.
        $app['oauth2.default_options'] = array(
            'expires_in' => 3600,
            'refresh_token' => true,
            'orm' => array(
                'connection' => 'default',
                'dev' => true,
                'path' => __DIR__ . '/Entity',
            ),
        );

        // Initializer to merge supplied options with default options.
        $app['oauth2.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }
            $initialized = true;

            if (!isset($app['oauth2.options'])) {
                $app['oauth2.options'] = $app['oauth2.default_options'];
            }
            $app['oauth2.options'] = array_replace($app['oauth2.default_options'], $app['oauth2.options']);
        });

        // Return an instance of Doctrine ORM entity manager.
        $app['oauth2.orm'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            $options = $app['oauth2.options']['orm'];
            $conn = $app['dbs'][$options['connection']];
            $config = Setup::createAnnotationMetadataConfiguration(array($options['path']), $options['dev']);
            $event_manager = $app['dbs.event_manager'][$options['connection']];

            return EntityManager::create($conn, $config, $event_manager);
        });

        // Shortcut for response_type.
        $response_type = array(
            'code' => 'Pantarei\OAuth2\ResponseType\CodeResponseType',
            'token' => 'Pantarei\OAuth2\ResponseType\TokenResponseType',
        );
        foreach ($response_type as $name => $class) {
            $app['oauth2.response_type.' . $name] = $class;
        }


        // Shortcut for grant_type.
        $grant_type = array(
            'authorization_code' => 'Pantarei\OAuth2\GrantType\AuthorizationCodeGrantType',
            'client_credentials' => 'Pantarei\OAuth2\GrantType\ClientCredentialsGrantType',
            'password' => 'Pantarei\OAuth2\GrantType\PasswordGrantType',
            'refresh_token' => 'Pantarei\OAuth2\GrantType\RefreshTokenGrantType',
        );
        foreach ($grant_type as $name => $class) {
            $app['oauth2.grant_type.' . $name] = $class;
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
