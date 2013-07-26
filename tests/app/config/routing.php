<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Resource endpoint.
$app->match('/oauth2/resource/username', function (Request $request, Application $app) {
    return $app['pantarei_oauth2.resource_controller']->usernameAction($request);
});

// Token endpoint.
$app->post('/oauth2/token', function (Request $request, Application $app) {
    return $app['pantarei_oauth2.token_controller']->tokenAction($request);
});

// Authorization endpoint.
$app->get('/oauth2/authorize', function (Request $request, Application $app) {
    return $app['pantarei_oauth2.authorize_controller']->authorizeAction($request);
});

// Form login.
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});
