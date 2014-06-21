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
use Symfony\Component\HttpKernel\Client;

// Hello World!!
$app->get('/', function (Request $request) use ($app) {
    if (!$app['session']->isStarted()) {
        $app['session']->start();
    }

    $acg_path = $app['url_generator']->generate('oauth2_authorize_form', array(
        'response_type' => 'code',
        'client_id' => 'acg',
        'redirect_uri' => 'http://localhost:8000/response_type/code',
        'scope' => 'demoscope1',
        'state' => $app['session']->getId(),
    ));

    $ig_path = $app['url_generator']->generate('oauth2_authorize_form', array(
        'response_type' => 'token',
        'client_id' => 'ig',
        'redirect_uri' => 'http://localhost:8000/response_type/token',
        'scope' => 'demoscope1',
        'state' => $app['session']->getId(),
    ));
    $ropcg_path = $app['url_generator']->generate('grant_type_password');

    return $app['twig']->render('index.html.twig', array(
        'acg_path' => $acg_path,
        'ig_path' => $ig_path,
        'ropcg_path' => $ropcg_path,
    ));
})->bind('index');

// Form login.
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Authorization endpoint, HTTP Basic authentication.
$app->get('/oauth2/authorize/http', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_http');

// Authorization endpoint, form login.
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

// Debug, authorization code grant, authorization endpoint.
$app->get('/response_type/code', function (Request $request, Application $app) {
    $code = $request->query->get('code');
    $submit_path = $app['url_generator']->generate('grant_type_authorization_code', array(
        'code' => $code,
    ));

    return $app['twig']->render('response_type/code.html.twig', array(
        'error' => $app['security.last_error']($request),
        'code' => $code,
        'submit_path' => $submit_path,
    ));
})->bind('response_type_code');

// Debug, authorization code grant, token endpoint.
$app->get('/grant_type/authorization_code', function (Request $request, Application $app) {
    $parameters = array(
        'grant_type' => 'authorization_code',
        'code' => $request->query->get('code'),
        'redirect_uri' => 'http://localhost:8000/response_type/code',
        'client_id' => 'acg',
        'client_secret' => 'uoce8AeP',
    );
    $server = array();
    $client = new Client($app);
    $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
    $token_response = json_decode($client->getResponse()->getContent(), true);

    $access_token = $token_response['access_token'];
    $resource_path = $app['url_generator']->generate('resource', array(
        'access_token' => $access_token,
    ));

    return $app['twig']->render('grant_type/authorization_code.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token' => $access_token,
        'resource_path' => $resource_path,
    ));
})->bind('grant_type_authorization_code');

// Debug, implicit grant, authorize endpoint.
$app->get('/response_type/token', function (Request $request, Application $app) {
    $access_token = $request->query->get('access_token');
    $resource_path = $app['url_generator']->generate('resource', array(
        'access_token' => $access_token,
    ));

    return $app['twig']->render('response_type/token.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token' => $access_token,
        'resource_path' => $resource_path,
    ));
})->bind('response_type_token');

// Debug, resource owner password credentials grant, token endpoint.
$app->get('/grant_type/password', function (Request $request, Application $app) {
    $parameters = array(
        'grant_type' => 'password',
        'username' => 'demousername1',
        'password' => 'demopassword1',
        'scope' => 'demoscope1',
        'state' => $app['session']->getId(),
    );
    $server = array(
        'PHP_AUTH_USER' => 'ropcg',
        'PHP_AUTH_PW' => 'Eevahph6',
    );
    $client = new Client($app);
    $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
    $token_response = json_decode($client->getResponse()->getContent(), true);

    $access_token = $token_response['access_token'];
    $resource_path = $app['url_generator']->generate('resource', array(
        'access_token' => $access_token,
    ));

    return $app['twig']->render('grant_type/password.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token' => $access_token,
        'resource_path' => $resource_path,
    ));
})->bind('grant_type_password');

// Debug, shared, resource endpoint.
$app->get('resource', function (Request $request, Application $app) {
    $parameters = array(
        'debug' => $request->query->get('access_token'),
    );
    $server = array(
        'HTTP_Authorization' => implode(' ', array('Bearer', $request->query->get('access_token'))),
    );
    $client = new Client($app);
    $crawler = $client->request('GET', '/oauth2/debug', $parameters, array(), $server);
    $debug_response = json_decode($client->getResponse()->getContent(), true);

    return $app['twig']->render('resource.html.twig', array(
        'error' => $app['security.last_error']($request),
        'debug' => $debug_response,
    ));
})->bind('resource');
