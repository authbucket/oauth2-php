<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Resource, index.
$app->get('/resource', function (Request $request) use ($app) {
    return $app['twig']->render('resource/index.html.twig');
})->bind('resource_index');
