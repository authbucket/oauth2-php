<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/oauth2', 'testbundle.oauth2_controller:oauth2IndexAction')
    ->bind('oauth2_index');

$app->get('/oauth2/login', 'testbundle.oauth2_controller:oauth2LoginAction')
    ->bind('oauth2_login');

$app->get('/oauth2/authorize/http', 'authbucket_oauth2.authorize_controller:authorizeAction')
    ->bind('oauth2_authorize_http');

$app->match('/oauth2/authorize', 'testbundle.authorize_controller:authorizeAction')
    ->bind('oauth2_authorize');

$app->match('/oauth2/token', 'authbucket_oauth2.token_controller:tokenAction')
    ->bind('oauth2_token');

$app->match('/oauth2/debug', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('oauth2_debug');
