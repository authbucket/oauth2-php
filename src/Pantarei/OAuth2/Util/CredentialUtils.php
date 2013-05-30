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
    public static function check(Request $request, Application $app)
    {
        $query = $request->request->all();

        // At least one (and only one) of client credentials method required.
        if (!$request->getUser() && !isset($query['client_id'])) {
            throw new InvalidRequestException();
        } elseif ($request->getUser() && isset($query['client_id'])) {
            throw new InvalidRequestException();
        }

        // Check with HTTP basic auth if exists.
        if ($request->getUser()) {
            $query['client_id'] = $request->getUser();
            $query['client_secret'] = $request->getPassword();
        }

        // Check with database record.
        $result = $app['oauth2.orm']->getRepository($app['oauth2.entity.clients'])->findOneBy(array(
            'client_id' => $query['client_id'],
            'client_secret' => $query['client_secret'],
        ));
        if ($result === null) {
            throw new InvalidClientException();
        }

        return true;
    }
}
