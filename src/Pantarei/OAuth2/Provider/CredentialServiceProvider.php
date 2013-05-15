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
      if (isset($_SERVER['PHP_AUTH_USER'])) {
        $query['client_id'] = $_SERVER['PHP_AUTH_USER'];
        $query['client_secret'] = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
      }
      return $query;
    });

    $app['oauth2.credential.check.client'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // At least one (and only one) of client credentials method required.
      if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($filtered_query['client_id'])) {
        throw new InvalidClientException();
      }
      elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($filtered_query['client_id'])) {
        throw new InvalidRequestException();
      }

      // Try HTTP basic auth.
      if (isset($_SERVER['PHP_AUTH_USER'])) {
        $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
          'client_id' => $_SERVER['PHP_AUTH_USER'],
          'client_secret' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
        ));
        if ($result == NULL) {
          return FALSE;
        }
      }
      // Try POST
      elseif (isset($query['client_id'])) {
        $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
          'client_id' => $query['client_id'],
          'client_secret' => isset($query['client_secret']) ? $query['client_secret'] : '',
        ));
        if ($result == NULL) {
          return FALSE;
        }
      }

      return TRUE;
    });

    $app['oauth2.credential.check.resource_owner'] = $app->protect(function ($query, $filtered_query) use ($app) {
      // username and password are required.
      if (!isset($filtered_query['username']) || !isset($filtered_query['password'])) {
        throw new InvalidRequestException();
      }

      // If username and password invalid we should stop here.
      $result = $app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Users')->findOneBy(array(
        'username' => $filtered_query['username'],
        'password' => $filtered_query['password'],
      ));
      if ($result == NULL) {
        throw new InvalidGrantException();
      }

      return TRUE;
    });
  }

  public function boot(Application $app)
  {
  }
}
