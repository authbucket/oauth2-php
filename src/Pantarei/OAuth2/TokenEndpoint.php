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

  private $grant_type;

  public function __construct($request, $app, $grant_type)
  {
    $this->request = $request;
    $this->app = $app;
    $this->grant_type = $grant_type;
  }

  public static function create(Request $request, Application $app)
  {
    CredentialUtils::check($request, $app);
    $grant_type = ParameterUtils::checkGrantType($request, $app)::create($request, $app);
    return new self($request, $app, $grant_type);

  }

  public function getResponse()
  { 
    return $this->grant_type->getResponse($this->request, $this->app);
  }
}
