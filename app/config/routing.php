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

// Hello World!!
$app->get('/', function (Request $request) use ($app) {
    return $app['twig']->render('index.html.twig');
})->bind('index');

// Form login.
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Authorization endpoint.
$app->get('/oauth2/authorize/http', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_http');
$app->get('/oauth2/authorize/form', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_form');

// Token endpoint.
$app->post('/oauth2/token', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.token_controller']->tokenAction($request);
})->bind('oauth2_token');

// Debug endpoint.
$app->match('/oauth2/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('oauth2_debug');

$app->get('/authorization_code', function (Request $request, Application $app) {
    return $app['twig']->render('authorization_code.html.twig', array(
        'authorization_code' => $request->query->get('code'),
        'error' => $app['security.last_error']($request),
    ));
})->bind('authorization_code');
