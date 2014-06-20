<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app['security.firewalls'] = array(
    // The login_path path must always be defined outside the secured area.
    // @link http://silex.sensiolabs.org/doc/providers/security.html#securing-a-path-with-a-form
    'login' => array(
        'pattern' => '^/login$',
        'anonymous' => true,
    ),
    // The authorization server MUST first verify the identity of the resource
    // owner. The way in which the authorization server authenticates the
    // resource owner (e.g., username and password login, session cookies) is
    // beyond the scope of this specification.
    // @link http://tools.ietf.org/html/rfc6749#section-3.1
    'authorize' => array(
        'pattern' => '^/oauth2/authorize',
        'logout' => array(
            'logout_path' => '/oauth2/authorize/logout',
        ),
        'form' => array(
            'login_path' => '/login',
            'check_path' => '/oauth2/authorize/login_check',
        ),
        'http' => true,
        'users' => array(
            'demousername1' => array('ROLE_USER', 'demopassword1'),
            'demousername2' => array('ROLE_USER', 'demopassword2'),
            'demousername3' => array('ROLE_USER', 'demopassword3'),
        ),
    ),
    // The authorization server MUST support the HTTP Basic authentication
    // scheme for authenticating clients that were issued a client password.
    // Alternatively, the authorization server MAY support including the client
    // credentials in the request-body.
    // @link http://tools.ietf.org/html/rfc6749#section-2.3.1
    'token' => array(
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
    'debug' => array(
        'pattern' => '^/oauth2/debug$',
        'oauth2_resource' => true,
    ),
);
