<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Util\Filter;
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
$app->get('/oauth2/authorize', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.authorize_controller']->authorizeAction($request);
})->bind('oauth2_authorize');

// OAuth2, Authorization endpoint, scope confirmation page.
$app->match('/oauth2/authorize/scope', function (Request $request, Application $app) {
    $query = $request->query->get('query', array());

    $form = $app['form.factory']->createBuilder('form')->getForm();
    $form->handleRequest($request);

    if ($form->isValid()) {
        $clientId = $query['client_id'] ?: '';
        $username = $app['security']->getToken()->getUser()->getUsername();

        $scope = $query['scope'] ?: array();
        if (!$scope || !Filter::filter(array('scope' => $scope))) {
            throw new ServerErrorException();
        }
        $scope = preg_split('/\s+/', $query['scope']);

        $scopeSupported = array();
        $scopeManager = $app['authbucket_oauth2.model_manager.factory']
            ->getModelManager('scope');
        $result = $scopeManager->findScopes();
        if ($result !== null) {
            foreach ($result as $row) {
                $scopeSupported[] = $row->getScope();
            }
        }
        if (array_intersect($scope, $scopeSupported) !== $scope) {
            throw new ServerErrorException();
        }

        $authorizeManager = $app['authbucket_oauth2.model_manager.factory']
            ->getModelManager('authorize');
        $authorize = $authorizeManager->findAuthorizeByClientIdAndUsername($clientId, $username);
        if ($authorize === null) {
            $authorize = $authorizeManager->createAuthorize();
        }

        $authorize->setClientId($clientId)
            ->setUsername($username)
            ->setScope($scope);
        $authorizeManager->updateAuthorize($authorize);

        $authorizePath = $app['authbucket_oauth2.authorize_path'];
        $authorizePath = 0 === strpos($authorizePath, 'http')
            ? $authorizePath
            : Request::createFromGlobals()->getUriForPath($authorizePath);
        $url = Request::create($authorizePath, 'GET', $query)->getUri();

        return $app->redirect($url);
    }

    // display the form
    return $app['twig']->render('oauth2/authorize/scope.html.twig', array(
        'client_id' => $query['client_id'],
        'scopes' => preg_split('/\s+/', $query['scope']),
        'form' => $form->createView(),
    ));
});

// OAuth2, Token endpoint.
$app->match('/oauth2/token', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.token_controller']->tokenAction($request);
})->bind('oauth2_token');

// OAuth2, Debug endpoint.
$app->match('/oauth2/debug', function (Request $request, Application $app) {
    return $app['authbucket_oauth2.debug_controller']->debugAction($request);
})->bind('oauth2_debug');
