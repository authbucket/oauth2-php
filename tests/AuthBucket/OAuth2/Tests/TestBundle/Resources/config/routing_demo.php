<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/demo', 'authbucket_oauth2.tests.demo_controller:demoIndexAction')
    ->bind('demo_index');

$app->get('/demo/authorize/code', 'authbucket_oauth2.tests.demo_controller:demoAuthorizeCodeAction')
    ->bind('demo_authorize_code');

$app->get('/demo/authorize/token', 'authbucket_oauth2.tests.demo_controller:demoAuthorizeTokenAction')
    ->bind('demo_authorize_token');

$app->get('/demo/response_type/code', 'authbucket_oauth2.tests.demo_controller:demoResponseTypeCodeAction')
    ->bind('demo_response_type_code');

$app->get('/demo/response_type/token', 'authbucket_oauth2.tests.demo_controller:demoResponseTypeTokenAction')
    ->bind('demo_response_type_token');

$app->get('/demo/grant_type/authorization_code', 'authbucket_oauth2.tests.demo_controller:demoGrantTypeAuthorizationCodeAction')
    ->bind('demo_grant_type_authorization_code');

$app->get('/demo/grant_type/password', 'authbucket_oauth2.tests.demo_controller:demoGrantTypePasswordAction')
    ->bind('demo_grant_type_password');

$app->get('/demo/grant_type/client_credentials', 'authbucket_oauth2.tests.demo_controller:demoGrantTypeClientCredentialsAction')
    ->bind('demo_grant_type_client_credentials');

$app->get('/demo/grant_type/refresh_token', 'authbucket_oauth2.tests.demo_controller:demoGrantTypeRefreshTokenAction')
    ->bind('demo_grant_type_refresh_token');

$app->get('/demo/resource_type/model', 'authbucket_oauth2.tests.demo_controller:demoResourceTypeModelAction')
    ->bind('demo_resource_type_model');

$app->get('/demo/resource_type/debug_endpoint', 'authbucket_oauth2.tests.demo_controller:demoResourceTypeDebugEndpointAction')
    ->bind('demo_resource_type_debug_endpoint');
