<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

// Client, index.
$app->get('/client', function (Request $request) use ($app) {
    return $app['twig']->render('client/index.html.twig');
})->bind('client_index');
