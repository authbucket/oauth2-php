<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app['security.user_provider.admin'] = $app['security.user_provider.inmemory._proto'](array(
    'admin' => array('ROLE_ADMIN', 'secrete'),
));

$app['security.user_provider.default'] = $app['security.user_provider.inmemory._proto'](array(
    'demousername1' => array('ROLE_USER', 'demopassword1'),
    'demousername2' => array('ROLE_USER', 'demopassword2'),
    'demousername3' => array('ROLE_USER', 'demopassword3'),
));

$app['security.firewalls'] = array(
    // Protect admin related links, e.g. refresh database.
    'admin' => array(
        'pattern' => '^/admin',
        'http' => true,
        'users' => $app['security.user_provider.admin'],
    ),
    // The login_path path must always be defined outside the secured area.
    // @link http://silex.sensiolabs.org/doc/providers/security.html#securing-a-path-with-a-form
    'oauth2_login' => array(
        'pattern' => '^/oauth2/login$',
        'anonymous' => true,
    ),
    // The authorization server MUST first verify the identity of the resource
    // owner. The way in which the authorization server authenticates the
    // resource owner (e.g., username and password login, session cookies) is
    // beyond the scope of this specification.
    // @link http://tools.ietf.org/html/rfc6749#section-3.1
    'oauth2_authorize_http' => array(
        'pattern' => '^/oauth2/authorize/http$',
        'http' => true,
        'users' => $app['security.user_provider.default'],
    ),
    'oauth2_authorize' => array(
        'pattern' => '^/oauth2/authorize',
        'form' => array(
            'login_path' => '/oauth2/login',
            'check_path' => '/oauth2/authorize/login_check',
        ),
        'logout' => array(
            'logout_path' => '/oauth2/authorize/logout',
        ),
        'users' => $app['security.user_provider.default'],
    ),
    // The authorization server MUST support the HTTP Basic authentication
    // scheme for authenticating clients that were issued a client password.
    // Alternatively, the authorization server MAY support including the client
    // credentials in the request-body.
    // @link http://tools.ietf.org/html/rfc6749#section-2.3.1
    'oauth2_token' => array(
        'pattern' => '^/oauth2/token$',
        'oauth2_token' => true,
    ),
    // The resource server MUST validate the access token and ensure that it
    // has not expired and that its scope covers the requested resource. The
    // methods used by the resource server to validate the access token (as
    // well as any error responses) are beyond the scope of this specification
    // but generally involve an interaction or coordination between the
    // resource server and the authorization server.
    // @link http://tools.ietf.org/html/rfc6749#section-7
    'oauth2_debug' => array(
        'pattern' => '^/oauth2/debug$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('debug'),
        ),
    ),
    'resource_debug' => array(
        'pattern' => '^/resource/debug$',
        'oauth2_resource' => true,
    ),
    'resource_debug_model' => array(
        'pattern' => '^/resource/debug/model$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('demoscope1'),
        ),
    ),
    'resource_debug_debug_endpoint' => array(
        'pattern' => '^/resource/debug/debug_endpoint$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'token_path' => '/oauth2/token',
                'debug_path' => '/oauth2/debug',
                'client_id' => 'http://democlient1.com/',
                'client_secret' => 'demosecret1',
                'cache' => false,
            ),
        ),
    ),
);
