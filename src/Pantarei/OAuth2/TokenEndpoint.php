<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2;

use Pantarei\OAuth2\Util\CredentialUtils;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OAuth2 token endpoint.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TokenEndpoint
{
    private $request;

    private $app;

    private $controller;

    public function __construct($request, $app, $controller)
    {
        $this->request = $request;
        $this->app = $app;
        $this->controller = $controller;
    }

    public static function create(Request $request, Application $app)
    {
        $grant_type = ParameterUtils::checkGrantType($request, $app);

        $controller = $app['oauth2.grant_type.' . $grant_type]::create($request, $app);

        return new self($request, $app, $controller);
    }

    public function getResponse()
    { 
        return $this->controller->getResponse($this->request, $this->app);
    }
}
