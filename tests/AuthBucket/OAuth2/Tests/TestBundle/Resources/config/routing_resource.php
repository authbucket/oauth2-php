<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/resource', 'authbucket_oauth2.tests.resource_controller:resourceIndexAction')
    ->bind('resource');

$app->match('/resource/resource_type/model', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_resource_type_model');

$app->match('/resource/resource_type/debug_endpoint', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_resource_type_debug_endpoint');

$app->match('/resource/resource_type/debug_endpoint/cache', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_resource_type_debug_endpoint_cache');

$app->match('/resource/resource_type/debug_endpoint/invalid_options', 'authbucket_oauth2.debug_controller:debugAction')
    ->bind('resource_resource_type_debug_endpoint_invalid_options');
