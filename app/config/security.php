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
    'login_path' => array(
        'pattern' => '^/login$',
        'anonymous' => true,
    ),
    'resource' => array(
        'pattern' => '^/oauth2/resource',
        'oauth2_resource' => true,
        'stateless' => true,
    ),
    'token' => array(
        'pattern' => '^/oauth2/token',
        'oauth2_token' => true,
    ),
    'default' => array(
        'pattern' => '^/',
        'logout' => array(
            'logout_path' => '/logout',
        ),
        'form' => array(
            'login_path' => '/login',
            'check_path' => '/login_check',
        ),
        'http' => true,
        'users' => array(
            'demousername1' => array('ROLE_USER', 'demopassword1'),
            'demousername2' => array('ROLE_USER', 'demopassword2'),
            'demousername3' => array('ROLE_USER', 'demopassword3'),
        ),
    ),
);
