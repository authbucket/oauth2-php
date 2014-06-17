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
    'auth_login' => array(
        'pattern' => '^/auth/login$',
        'anonymous' => true,
    ),
    'auth_resource' => array(
        'pattern' => '^/auth/oauth2/resource/username',
        'oauth2_resource' => true,
        'stateless' => true,
    ),
    'auth_token' => array(
        'pattern' => '^/auth/oauth2/token',
        'oauth2_token' => true,
    ),
    'auth_default' => array(
        'pattern' => '^/auth',
        'logout' => array(
            'logout_path' => '/auth/logout',
        ),
        'form' => array(
            'login_path' => '/auth/login',
            'check_path' => '/auth/login_check',
        ),
        'http' => true,
        'users' => array(
            'demousername1' => array('ROLE_USER', 'demopassword1'),
            'demousername2' => array('ROLE_USER', 'demopassword2'),
            'demousername3' => array('ROLE_USER', 'demopassword3'),
        ),
    ),
);
