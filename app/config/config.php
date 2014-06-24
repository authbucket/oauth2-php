<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/security.php';

$app['debug'] = true;

$app['twig.path'] = array(
    __DIR__ . '/../Resources/views',
);

$app['authbucket_oauth2.model'] = array(
    'access_token' => 'AuthBucket\\OAuth2\\Tests\\Entity\\AccessToken',
    'authorize' => 'AuthBucket\\OAuth2\\Tests\\Entity\\Authorize',
    'client' => 'AuthBucket\\OAuth2\\Tests\\Entity\\Client',
    'code' => 'AuthBucket\\OAuth2\\Tests\\Entity\\Code',
    'refresh_token' => 'AuthBucket\\OAuth2\\Tests\\Entity\\RefreshToken',
    'scope' => 'AuthBucket\\OAuth2\\Tests\\Entity\\Scope',
);
