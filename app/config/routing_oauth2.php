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

// OAuth2, index.
$app->get('/oauth2', function (Request $request) use ($app) {
    return $app['twig']->render('oauth2/index.html.twig');
})->bind('oauth2_index');

// OAuth2, Form login.
$app->get('/oauth2/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
    ));
})->bind('oauth2_login');

// OAuth2, Authorization endpoint, HTTP Basic authentication.
$app->get('/oauth2/authorize/http', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_http');

// OAuth2, Authorization endpoint, form login.
$app->get('/oauth2/authorize/form', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_form');

// OAuth2, Token endpoint.
$app->post('/oauth2/token', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.token_controller']->tokenAction($request);
})->bind('oauth2_token');

// OAuth2, Debug endpoint.
$app->match('/oauth2/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('oauth2_debug');
