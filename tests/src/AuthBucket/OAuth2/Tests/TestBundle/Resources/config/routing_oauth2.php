<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Exception\InvalidScopeException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// OAuth2, index.
$app->get('/oauth2', function (Request $request) use ($app) {
    return $app['twig']->render('oauth2/index.html.twig');
})->bind('oauth2_index');

// OAuth2, Form login.
$app->get('/oauth2/login', function (Request $request) use ($app) {
    return $app['twig']->render('oauth2/login.html.twig', array(
        'error' => $app['security.last_error']($request),
    ));
})->bind('oauth2_login');

// OAuth2, Authorization endpoint, HTTP Basic authentication.
$app->get('/oauth2/authorize/http', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize_http');

// OAuth2, Authorization endpoint, form login.
$app->match('/oauth2/authorize', function (Request $request, Application $app) {
    // We only handle non-authorized scope here.
    try {
        return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
    } catch (InvalidScopeException $exception) {
        $message = unserialize($exception->getMessage());
        if ($message['error_description'] !== 'The requested scope is invalid.') {
            throw $exception;
        }
    }

    // Fetch parameters, which already checked.
    $clientId = $request->query->get('client_id');
    $username = $app['security']->getToken()->getUser()->getUsername();
    $scope = preg_split('/\s+/', $request->query->get('scope', ''));

    // Create form.
    $form = $app['form.factory']->createBuilder('form')->getForm();
    $form->handleRequest($request);

    // Save authorized scope if submitted by POST.
    if ($form->isValid()) {
        $modelManager = $app['authbucket_oauth2.model_manager.factory'];
        $authorizeManager = $modelManager->getModelManager('authorize');
        $authorize = $authorizeManager->findAuthorizeByClientIdAndUsername($clientId, $username);

        // Update existing authorization if possible, else create new.
        if ($authorize === null) {
            $authorize = $authorizeManager->createAuthorize();
        }

        // Save authorization.
        $authorize->setClientId($clientId)
            ->setUsername($username)
            ->setScope(array_merge($authorize->getScope(), $scope));
        $authorizeManager->updateAuthorize($authorize);

        // Back to this path, with original GET parameters.
        return $app->redirect($request->getRequestUri());
    }

    // Display the form.
    $authorizationRequest = $request->query->all();

    return $app['twig']->render('oauth2/authorize.html.twig', array(
        'client_id' => $clientId,
        'username' => $username,
        'scopes' => $scope,
        'form' => $form->createView(),
        'authorization_request' => $authorizationRequest,
    ));
})->bind('oauth2_authorize');

// OAuth2, Token endpoint.
$app->match('/oauth2/token', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.token_controller']->tokenAction($request);
})->bind('oauth2_token');

// OAuth2, Debug endpoint.
$app->match('/oauth2/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('oauth2_debug');
