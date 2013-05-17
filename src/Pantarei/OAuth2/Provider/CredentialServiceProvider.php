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

use Pantarei\OAuth2\Exception\InvalidClientException;
use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client credentials related utilities for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CredentialServiceProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {
    $app['oauth2.credential.fetch.client'] = $app->protect(function ($query) use ($app) {
      $request = Request::createFromGlobals();

      if ($request->getUser()) {
        $query['client_id'] = $request->getUser();
        $query['client_secret'] = $request->getPassword();
      }

      return $query;
    });

    $app['oauth2.credential.check.client'] = $app->protect(function ($query, $filtered_query) use ($app) {
      $request = Request::createFromGlobals();

      // At least one (and only one) of client credentials method required.
      if (!$request->getUser() && !isset($filtered_query['client_id'])) {
        throw new InvalidClientException();
      }
      elseif ($request->getUser() && isset($filtered_query['client_id'])) {
        throw new InvalidRequestException();
      }

      // Try HTTP basic auth.
      if ($request->getUser()) {
        $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
          'client_id' => $request->getUser(),
          'client_secret' => $request->getPassword(),
        ));
        if ($result == NULL) {
          return FALSE;
        }
      }
      // Try POST
      elseif (isset($query['client_id'])) {
        $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
          'client_id' => $query['client_id'],
          'client_secret' => $query['client_secret'],
        ));
        if ($result == NULL) {
          return FALSE;
        }
      }

      return TRUE;
    });

  }

  public function boot(Application $app)
  {
  }
}
