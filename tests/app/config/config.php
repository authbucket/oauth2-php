<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/security.php';

$app['twig.path'] = array(
    __DIR__ . '/../Resources/views',
);

$app['pantarei_oauth2.model'] = array(
    'access_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\AccessToken',
    'authorize' => 'Pantarei\\OAuth2\\Tests\\Entity\\Authorize',
    'client' => 'Pantarei\\OAuth2\\Tests\\Entity\\Client',
    'code' => 'Pantarei\\OAuth2\\Tests\\Entity\\Code',
    'refresh_token' => 'Pantarei\\OAuth2\\Tests\\Entity\\RefreshToken',
    'scope' => 'Pantarei\\OAuth2\\Tests\\Entity\\Scope',
);
