<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/AppKernel.php';

require __DIR__ . '/../app/config/config_prod.php';
require __DIR__ . '/../app/config/routing_prod.php';

$app->run();
