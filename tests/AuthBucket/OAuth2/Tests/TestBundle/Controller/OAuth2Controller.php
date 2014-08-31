<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle\Controller;

use AuthBucket\OAuth2\Exception\InvalidScopeException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class OAuth2Controller
{
    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('oauth2/index.html.twig');
    }

    public function loginAction(Request $request, Application $app)
    {
        $session = $request->getSession();

        $error = $app['security.last_error']($request);
        $_username = $session->get('_username');
        $_password = $session->get('_password');

        return $app['twig']->render('oauth2/login.html.twig', array(
            'error' => $error,
            '_username' => $_username,
            '_password' => $_password,
        ));
    }

    public function authorizeAction(Request $request, Application $app)
    {
        // We only handle non-authorized scope here.
        try {
            return $app['authbucket_oauth2.oauth2_controller']->authorizeAction($request);
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
            $modelManagerFactory = $app['authbucket_oauth2.model_manager.factory'];
            $authorizeManager = $modelManagerFactory->getModelManager('authorize');

            // Update existing authorization if possible, else create new.
            $authorize = $authorizeManager->readModelOneBy(array(
                'clientId' => $clientId,
                'username' => $username,
            ));
            if ($authorize === null) {
                $class = $authorizeManager->getClassName();
                $authorize = new $class();
                $authorize->setClientId($clientId)
                    ->setUsername($username)
                    ->setScope((array) $scope);
                $authorize = $authorizeManager->createModel($authorize);
            } else {
                $authorize->setClientId($clientId)
                    ->setUsername($username)
                    ->setScope(array_merge((array) $authorize->getScope(), $scope));
                $authorizeManager->updateAuthorize($authorize);
            }

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
    }
}
