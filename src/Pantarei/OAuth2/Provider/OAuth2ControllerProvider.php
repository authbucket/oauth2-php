<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Provider;

use Pantarei\OAuth2\AuthorizationEndpoint;
use Pantarei\OAuth2\ResourceEndpoint;
use Pantarei\OAuth2\TokenEndpoint;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OAuth2 controller provider for Silex.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2ControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // Authorization endpoint.
        $controllers->get('/authorize', function (Request $request, Application $app) {
            $endpoint = AuthorizationEndpoint::create($request, $app);
            return $endpoint->getResponse();
        });

        // Token endpoint.
        $controllers->post('/token', function (Request $request, Application $app) {
            $endpoint = TokenEndpoint::create($request, $app);
            return $endpoint->getResponse();
        });

        // Resource endpoint.
        $controllers->get('/resource/{username}', function (Request $request, Application $app, $username) {
            $endpoint = ResourceEndpoint::create($request, $app);
            return $endpoint->getResponse($username);
        });

        return $controllers;
    }
}
