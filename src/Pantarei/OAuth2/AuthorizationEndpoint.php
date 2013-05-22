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

use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OAuth2 authorization endpoint.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationEndpoint
{
  private $request;

  private $app;
  
  private $response_type;

  public function __construct($request, $app, $response_type)
  {
    $this->request = $request;
    $this->app = $app;
    $this->response_type = $response_type;
  }

  public static function create(Request $request, Application $app)
  {
    $response_type = ParameterUtils::checkResponseType($request, $app)::create($request, $app);
    return new self($request, $app, $response_type);

  }

  public function getResponse()
  {
    return $this->response_type->getResponse($this->request, $this->app);
  }
}
