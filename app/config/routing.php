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
    return $app['twig']->render('html.html.twig', array(
        'head_title' => 'Hello World!!',
        'page' => $app['twig']->render('index.html.twig'),
    ));
});

// Form login.
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('html.html.twig', array(
        'head_title' => 'Form login',
        'page' => $app['twig']->render('login.html.twig', array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        )),
    ));
});

// Authorization endpoint.
$app->get('/oauth2/authorize', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
});

// Token endpoint.
$app->post('/oauth2/token', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.token_controller']->tokenAction($request);
});

// Debug endpoint.
$app->match('/oauth2/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
});
