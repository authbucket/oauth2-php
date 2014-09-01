<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/resource', 'authbucket_oauth2.tests.resource_controller:indexAction')
    ->bind('resource');

$app->match('/api/v1.0/resource/model', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('_resource_model');

$app->match('/api/v1.0/resource/debug_endpoint', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('_resource_debug_endpoint');

$app->match('/api/v1.0/resource/debug_endpoint/cache', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('_resource_debug_endpoint_cache');

$app->match('/api/v1.0/resource/debug_endpoint/invalid_options', 'authbucket_oauth2.oauth2_controller:debugAction')
    ->bind('_resource_debug_endpoint_invalid_options');
