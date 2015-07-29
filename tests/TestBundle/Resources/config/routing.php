<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/', 'authbucket_oauth2.tests.default_controller:indexAction')
    ->bind('index');

$app->get('/admin/refresh_database', 'authbucket_oauth2.tests.default_controller:adminRefreshDatabaseAction')
    ->bind('admin_refresh_database');

$app->get('/getting-started', 'authbucket_oauth2.tests.default_controller:gettingStartedIndexAction')
    ->bind('getting-started');

$app->get('/demo', 'authbucket_oauth2.tests.demo_controller:indexAction')
    ->bind('demo');

$app->get('/demo/login', 'authbucket_oauth2.tests.demo_controller:loginAction')
    ->bind('demo_login');

$app->match('/demo/authorize', 'authbucket_oauth2.tests.demo_controller:authorizeAction')
    ->bind('demo_authorize');

$app->get('/demo/request/code', 'authbucket_oauth2.tests.demo_controller:requestCodeAction')
    ->bind('demo_request_code');

$app->get('/demo/request/token', 'authbucket_oauth2.tests.demo_controller:requestTokenAction')
    ->bind('demo_request_token');

$app->get('/demo/response_type/code', 'authbucket_oauth2.tests.demo_controller:responseTypeCodeAction')
    ->bind('demo_response_type_code');

$app->get('/demo/response_type/token', 'authbucket_oauth2.tests.demo_controller:responseTypeTokenAction')
    ->bind('demo_response_type_token');

$app->get('/demo/grant_type/authorization_code', 'authbucket_oauth2.tests.demo_controller:grantTypeAuthorizationCodeAction')
    ->bind('demo_grant_type_authorization_code');

$app->get('/demo/grant_type/password', 'authbucket_oauth2.tests.demo_controller:grantTypePasswordAction')
    ->bind('demo_grant_type_password');

$app->get('/demo/grant_type/client_credentials', 'authbucket_oauth2.tests.demo_controller:grantTypeClientCredentialsAction')
    ->bind('demo_grant_type_client_credentials');

$app->get('/demo/grant_type/refresh_token', 'authbucket_oauth2.tests.demo_controller:grantTypeRefreshTokenAction')
    ->bind('demo_grant_type_refresh_token');

$app->get('/demo/resource_type/model', 'authbucket_oauth2.tests.demo_controller:resourceTypeModelAction')
    ->bind('demo_resource_type_model');

$app->get('/demo/resource_type/debug_endpoint', 'authbucket_oauth2.tests.demo_controller:resourceTypeDebugEndpointAction')
    ->bind('demo_resource_type_debug_endpoint');

$app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
    ->bind('api_oauth2_authorize');

$app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
    ->bind('api_oauth2_token');

$app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_oauth2_debug');

$app->match('/api/resource/model', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_resource_model');

$app->match('/api/resource/debug_endpoint', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_resource_debug_endpoint');

$app->match('/api/resource/debug_endpoint/cache', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_resource_debug_endpoint_cache');

$app->match('/api/resource/debug_endpoint/invalid_options', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('api_resource_debug_endpoint_invalid_options');
