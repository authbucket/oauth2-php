<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension;

use Pantarei\OAuth2\OAuth2TypeInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the abstract class for token type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class TokenType implements OAuth2TypeInterface
{
  public function __construct(Request $request, Application $app)
  {
    return TRUE;
  }

  public function getResponse()
  {
    return new Response();
  }

  public function getParent()
  {
    return NULL;
  }

  public function getName()
  {
    return 'token_type';
  }

}
