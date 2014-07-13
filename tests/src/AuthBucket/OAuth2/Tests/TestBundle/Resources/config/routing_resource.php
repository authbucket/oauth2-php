<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Resource, index.
$app->get('/resource', function (Request $request) use ($app) {
    return $app['twig']->render('resource/index.html.twig');
})->bind('resource_index');

// Resource, Debug endpoint.
$app->match('/resource/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug');

// Resource, Debug endpoint, model.
$app->match('/resource/debug/model', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug_model');

// Resource, Debug endpoint, debug endpoint.
$app->match('/resource/debug/debug_endpoint', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug_debug_endpoint');

// Resource, Debug endpoint, debug endpoint, cache enabled.
$app->match('/resource/debug/debug_endpoint/cache', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug_debug_endpoint_cache');

// Resource, Debug endpoint, debug endpoint, invalid options.
$app->match('/resource/debug/debug_endpoint/invalid_options', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug_debug_endpoint_invalid_options');

// Resource, Debug endpoint, debug endpoint, invalid client.
$app->match('/resource/debug/debug_endpoint/invalid_client', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('resource_debug_debug_endpoint_invalid_client');
