<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/routing_client.php';
require __DIR__ . '/routing_demo.php';
require __DIR__ . '/routing_oauth2.php';
require __DIR__ . '/routing_resource.php';

$app->get('/', 'testbundle.default_controller:indexAction')
    ->bind('index');

$app->get('/admin/refresh_database', 'testbundle.default_controller:adminRefreshDatabaseAction')
    ->bind('admin_refresh_database');
