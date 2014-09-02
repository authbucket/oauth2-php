<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->get('/oauth2', 'authbucket_oauth2.tests.oauth2_controller:indexAction')
    ->bind('oauth2');

$app->get('/oauth2/login', 'authbucket_oauth2.tests.oauth2_controller:loginAction')
    ->bind('oauth2_login');

$app->match('/oauth2/authorize', 'authbucket_oauth2.tests.oauth2_controller:authorizeAction')
    ->bind('oauth2_authorize');
