<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/orm.php';
require __DIR__ . '/security.php';

$app['debug'] = true;

$app['twig.path'] = array(
    __DIR__ . '/../../tests/src/AuthBucket/OAuth2/Tests/TestBundle/Resources/views',
);

// We simply reuse the user provider that already created for authorize firewall
// here.
$app['authbucket_oauth2.user_provider'] = $app['security.user_provider.default'];
