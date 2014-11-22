<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/security_dev.php';

$app['security.firewalls'] += array(
    'api_resource_model' => array(
        'pattern' => '^/api/v1.0/resource/model$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('demoscope1'),
        ),
    ),
    'api_resource_debug_endpoint' => array(
        'pattern' => '^/api/v1.0/resource/debug_endpoint$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'debug_endpoint' => '/api/v1.0/oauth2/debug',
                'cache' => false,
            ),
        ),
    ),
    'api_resource_debug_endpoint_cache' => array(
        'pattern' => '^/api/v1.0/resource/debug_endpoint/cache$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'debug_endpoint' => '/api/v1.0/oauth2/debug',
                'cache' => true,
            ),
        ),
    ),
    'api_resource_debug_endpoint_invalid_options' => array(
        'pattern' => '^/api/v1.0/resource/debug_endpoint/invalid_options$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'debug_endpoint' => '',
                'cache' => true,
            ),
        ),
    ),
);
