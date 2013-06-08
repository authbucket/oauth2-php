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

use Pantarei\OAuth2\GrantType\AuthorizationCodeGrantType;
use Pantarei\OAuth2\GrantType\ClientCredentialsGrantType;
use Pantarei\OAuth2\GrantType\PasswordGrantType;
use Pantarei\OAuth2\GrantType\RefreshTokenGrantType;
use Pantarei\OAuth2\ResponseType\CodeResponseType;
use Pantarei\OAuth2\ResponseType\TokenResponseType;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;

/**
 * A simple Doctrine ORM service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ServiceProvider extends SecurityServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

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
        parent::boot($app);
    }
}
