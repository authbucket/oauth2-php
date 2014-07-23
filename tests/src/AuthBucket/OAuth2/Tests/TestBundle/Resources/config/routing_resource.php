<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/resource', 'testbundle.resource_controller:resourceIndexAction')
    ->bind('resource_index');

$app->match('/resource/resource_type/model', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_debug_model');

$app->match('/resource/resource_type/debug_endpoint', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_debug_debug_endpoint');

$app->match('/resource/resource_type/debug_endpoint/cache', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_debug_debug_endpoint_cache');

$app->match('/resource/resource_type/debug_endpoint/invalid_options', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_debug_debug_endpoint_invalid_options');

$app->match('/resource/resource_type/debug_endpoint/invalid_client', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_debug_debug_endpoint_invalid_client');
