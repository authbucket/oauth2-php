<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\Tools\SchemaTool;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

require __DIR__ . '/routing.php';

if (!$app['session']->isStarted()) {
    $app['session']->start();
}

// Index.
$app->get('/', function (Request $request) use ($app) {
    return $app['twig']->render('index.html.twig');
})->bind('index');

// Flush database.
$app->get('/admin/refresh_database', function (Request $request) use ($app) {
    $connection = $app['db'];
    $em = $app['authbucket_oauth2.orm'];

    $params = $connection->getParams();
    $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

    try {
        $connection->getSchemaManager()->dropDatabase($name);
        $connection->getSchemaManager()->createDatabase($name);
        $connection->close();
    } catch (\Exception $e) {
        return 1;
    }

    $classes = array();
    foreach ($app['authbucket_oauth2.model'] as $class) {
        $classes[] = $em->getClassMetadata($class);
    }

    PersistentObject::setObjectManager($em);
    $tool = new SchemaTool($em);
    $tool->dropSchema($classes);
    $tool->createSchema($classes);

    $purger = new ORMPurger();
    $executor = new ORMExecutor($em, $purger);

    $loader = new Loader();
    $loader->loadFromDirectory(__DIR__ . '/../../tests/src/AuthBucket/OAuth2/Tests/DataFixtures/ORM');
    $executor->execute($loader->getFixtures());

    return $app->redirect($app['url_generator']->generate('index'));
})->bind('admin_refresh_database');

// Form login.
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
    ));
})->bind('login');

// Debug, authorization code grant, authorization endpoint.
$app->get('/response_type/code', function (Request $request, Application $app) {
    $parameters = array(
        'response_type' => 'code',
        'client_id' => 'acg',
        'redirect_uri' => $request->getUriForPath('/response_type/code'),
        'scope' => 'demoscope1',
        'state' => $app['session']->getId(),
    );
    $server = array(
        'PHP_AUTH_USER' => 'demousername1',
        'PHP_AUTH_PW' => 'demopassword1',
    );
    $client = new Client($app);
    $crawler = $client->request('GET', '/oauth2/authorize/http', $parameters, array(), $server);
    $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
    $authorizationResponse = $authResponse->query->all();
    $authorizationRequest = get_object_vars($client->getRequest());

    $tokenPath = $app['url_generator']->generate('grant_type_authorization_code', array(
        'code' => $authorizationResponse['code'],
        'state' => $authorizationResponse['state'],
    ));

    return $app['twig']->render('response_type/code.html.twig', array(
        'error' => $app['security.last_error']($request),
        'authorization_response' => $authorizationResponse,
        'authorization_request' => $authorizationRequest,
        'token_path' => $tokenPath,
    ));
})->bind('response_type_code');

// Debug, authorization code grant, token endpoint.
$app->get('/grant_type/authorization_code', function (Request $request, Application $app) {
    $parameters = array(
        'grant_type' => 'authorization_code',
        'code' => $request->query->get('code'),
        'redirect_uri' => $request->getUriForPath('/response_type/code'),
        'client_id' => 'acg',
        'client_secret' => 'uoce8AeP',
        'state' => $request->query->get('state'),
    );
    $server = array();
    $client = new Client($app);
    $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
    $accessTokenResponse = json_decode($client->getResponse()->getContent(), true);
    $accessTokenRequest = get_object_vars($client->getRequest());

    $resourcePath = $app['url_generator']->generate('resource', array(
        'access_token' => $accessTokenResponse['access_token'],
    ));
    $refreshPath = $app['url_generator']->generate('grant_type_refresh_token', array(
        'username' => 'acg',
        'password' => 'uoce8AeP',
        'refresh_token' => $accessTokenResponse['refresh_token'],
    ));

    return $app['twig']->render('grant_type/authorization_code.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token_response' => $accessTokenResponse,
        'access_token_request' => $accessTokenRequest,
        'resource_path' => $resourcePath,
        'refresh_path' => $refreshPath,
    ));
})->bind('grant_type_authorization_code');

// Debug, implicit grant, authorize endpoint.
$app->get('/response_type/token', function (Request $request, Application $app) {
    $parameters = array(
        'response_type' => 'token',
        'client_id' => 'ig',
        'redirect_uri' => $request->getUriForPath('/response_type/token'),
        'scope' => 'demoscope1',
        'state' => $app['session']->getId(),
    );
    $server = array(
        'PHP_AUTH_USER' => 'demousername1',
        'PHP_AUTH_PW' => 'demopassword1',
    );
    $client = new Client($app);
    $crawler = $client->request('GET', '/oauth2/authorize/http', $parameters, array(), $server);
    $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
    $accessTokenResponse = $authResponse->query->all();
    $accessTokenRequest = get_object_vars($client->getRequest());

    $resourcePath = $app['url_generator']->generate('resource', array(
        'access_token' => $accessTokenResponse['access_token'],
    ));

    return $app['twig']->render('response_type/token.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token_response' => $accessTokenResponse,
        'access_token_request' => $accessTokenRequest,
        'resource_path' => $resourcePath,
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
    $accessTokenResponse = json_decode($client->getResponse()->getContent(), true);
    $accessTokenRequest = get_object_vars($client->getRequest());

    $resourcePath = $app['url_generator']->generate('resource', array(
        'access_token' => $accessTokenResponse['access_token'],
    ));
    $refreshPath = $app['url_generator']->generate('grant_type_refresh_token', array(
        'username' => 'ropcg',
        'password' => 'Eevahph6',
        'refresh_token' => $accessTokenResponse['refresh_token'],
    ));

    return $app['twig']->render('grant_type/password.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token_response' => $accessTokenResponse,
        'access_token_request' => $accessTokenRequest,
        'resource_path' => $resourcePath,
        'refresh_path' => $refreshPath,
    ));
})->bind('grant_type_password');

// Debug, client credentials grant, token endpoint.
$app->get('/grant_type/client_credentials', function (Request $request, Application $app) {
    $parameters = array(
        'grant_type' => 'client_credentials',
        'scope' => 'demoscope1',
    );
    $server = array(
        'PHP_AUTH_USER' => 'ccg',
        'PHP_AUTH_PW' => 'yib6aiFe',
    );
    $client = new Client($app);
    $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
    $accessTokenResponse = json_decode($client->getResponse()->getContent(), true);
    $accessTokenRequest = get_object_vars($client->getRequest());

    $resourcePath = $app['url_generator']->generate('resource', array(
        'access_token' => $accessTokenResponse['access_token'],
    ));
    $refreshPath = $app['url_generator']->generate('grant_type_refresh_token', array(
        'username' => 'ccg',
        'password' => 'yib6aiFe',
        'refresh_token' => $accessTokenResponse['refresh_token'],
    ));

    return $app['twig']->render('grant_type/client_credentials.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token_response' => $accessTokenResponse,
        'access_token_request' => $accessTokenRequest,
        'resource_path' => $resourcePath,
        'refresh_path' => $refreshPath,
    ));
})->bind('grant_type_client_credentials');

// Debug, refresh token grant, token endpoint.
$app->get('/grant_type/refresh_token', function (Request $request, Application $app) {
    $resourceRequest = $request->query->all();
    $parameters = array(
        'grant_type' => 'refresh_token',
        'refresh_token' => $resourceRequest['refresh_token'],
    );
    $server = array(
        'PHP_AUTH_USER' => $resourceRequest['username'],
        'PHP_AUTH_PW' => $resourceRequest['password'],
    );
    $client = new Client($app);
    $crawler = $client->request('POST', '/oauth2/token', $parameters, array(), $server);
    $accessTokenResponse = json_decode($client->getResponse()->getContent(), true);
    $accessTokenRequest = get_object_vars($client->getRequest());

    $resourcePath = $app['url_generator']->generate('resource', array(
        'access_token' => $accessTokenResponse['access_token'],
    ));
    $refreshPath = $app['url_generator']->generate('grant_type_refresh_token', array(
        'username' => $resourceRequest['username'],
        'password' => $resourceRequest['password'],
        'refresh_token' => $accessTokenResponse['refresh_token'],
    ));

    return $app['twig']->render('grant_type/refresh_token.html.twig', array(
        'error' => $app['security.last_error']($request),
        'access_token_response' => $accessTokenResponse,
        'access_token_request' => $accessTokenRequest,
        'resource_path' => $resourcePath,
        'refresh_path' => $refreshPath,
    ));
})->bind('grant_type_refresh_token');

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
    $debugResponse = json_decode($client->getResponse()->getContent(), true);
    $debugRequest = get_object_vars($client->getRequest());

    return $app['twig']->render('resource.html.twig', array(
        'error' => $app['security.last_error']($request),
        'debug_response' => $debugResponse,
        'debug_request' => $debugRequest,
    ));
})->bind('resource');
