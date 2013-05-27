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
            'entity' => array(
                'AccessTokens' => 'Pantarei\OAuth2\Entity\AccessTokens',
                'Authorizes' => 'Pantarei\OAuth2\Entity\Authorizes',
                'Clients' => 'Pantarei\OAuth2\Entity\Clients',
                'Codes' => 'Pantarei\OAuth2\Entity\Codes',
                'RefreshTokens' => 'Pantarei\OAuth2\Entity\RefreshTokens',
                'Scopes' => 'Pantarei\OAuth2\Entity\Scopes',
                'Users' => 'Pantarei\OAuth2\Entity\Users',
            ),
            'response_type' => array(
                'code' => 'Pantarei\OAuth2\Extension\ResponseType\CodeResponseType',
                'token' => 'Pantarei\OAuth2\Extension\ResponseType\TokenResponseType',
            ),
            'grant_type' => array(
                'authorization_code' => 'Pantarei\OAuth2\Extension\GrantType\AuthorizationCodeGrantType',
                'client_credentials' => 'Pantarei\OAuth2\Extension\GrantType\ClientCredentialsGrantType',
                'password' => 'Pantarei\OAuth2\Extension\GrantType\PasswordGrantType',
                'refresh_token' => 'Pantarei\OAuth2\Extension\GrantType\RefreshTokenGrantType',
            ),
            'token_type' => array(
                'bearer' => 'Pantarei\OAuth2\Extension\TokenType\BearerTokenType',
                'mac' => 'Pantarei\OAuth2\Extension\TokenType\MacTokenType',
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

        // Shortcut for entity.
        $app['oauth2.entity'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            return $app['oauth2.options']['entity'];
        });

        // Shortcut for response_type.
        $app['oauth2.response_type'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            return $app['oauth2.options']['response_type'];
        });

        // Shortcut for grant_type.
        $app['oauth2.grant_type'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            return $app['oauth2.options']['grant_type'];
        });

        // Shortcut for token_type.
        $app['oauth2.token_type'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            return $app['oauth2.options']['token_type'];
        });

        // Shortcut for default token_type.
        $app['oauth2.token_type.default'] = $app->share(function ($app) {
            $app['oauth2.options.initializer']();

            $token_type = $app['oauth2.token_type'];
            return array_shift($token_type);
        });
    }

    public function boot(Application $app)
    {
    }
}
