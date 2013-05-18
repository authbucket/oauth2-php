<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Util;

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client credentials related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class CredentialUtils
{
  public static function check(Application $app)
  {
    $request = Request::createFromGlobals();

    // At least one (and only one) of client credentials method required.
    if (!$request->getUser() && !$request->request->get('client_id')) {
      throw new InvalidRequestException();
    }
    elseif ($request->getUser() && $request->request->get('client_id')) {
      throw new InvalidRequestException();
    }

    // Try HTTP basic auth.
    if ($request->getUser()) {
      $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $request->getUser(),
        'client_secret' => $request->getPassword(),
      ));
      if ($result === NULL) {
        throw new InvalidClientException();
      }
    }
    // Try POST
    elseif ($request->request->get('client_id')) {
      $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
        'client_id' => $request->request->get('client_id'),
        'client_secret' => $request->request->get('client_secret'),
      ));
      if ($result === NULL) {
        throw new InvalidClientException();
      }
    }

    return TRUE;
  }
}
