<?php

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder();
});

$app['security.user_provider.default'] = $app->share(function ($app) {
    return $app['authbucket_oauth2.model_manager.factory']->getModelManager('user');
});

$app['security.user_provider.admin'] = $app['security.user_provider.inmemory._proto'](array(
    'admin' => array('ROLE_ADMIN', 'secrete'),
));

$app['security.firewalls'] = array(
    'admin' => array(
        'pattern' => '^/admin',
        'http' => true,
        'users' => $app['security.user_provider.admin'],
    ),
    'demo_login' => array(
        'pattern' => '^/demo/login$',
        'anonymous' => true,
    ),
    'demo_authorize' => array(
        'pattern' => '^/demo/authorize',
        'remember_me' => true,
        'form' => array(
            'login_path' => '/demo/login',
            'check_path' => '/demo/authorize/login_check',
        ),
        'logout' => array(
            'logout_path' => '/demo/authorize/logout',
            'target_url' => '/demo',
        ),
        'users' => $app['security.user_provider.default'],
    ),
    'api_oauth2_authorize' => array(
        'pattern' => '^/api/oauth2/authorize$',
        'http' => true,
        'users' => $app['security.user_provider.default'],
    ),
    'api_oauth2_token' => array(
        'pattern' => '^/api/oauth2/token$',
        'oauth2_token' => true,
    ),
    'api_oauth2_debug' => array(
        'pattern' => '^/api/oauth2/debug$',
        'oauth2_resource' => true,
    ),
    'api_resource_model' => array(
        'pattern' => '^/api/resource/model$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('demoscope1'),
        ),
    ),
    'api_resource_debug_endpoint' => array(
        'pattern' => '^/api/resource/debug_endpoint$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'debug_endpoint' => '/api/oauth2/debug',
                'cache' => false,
            ),
        ),
    ),
    'api_resource_debug_endpoint_cache' => array(
        'pattern' => '^/api/resource/debug_endpoint/cache$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'debug_endpoint' => '/api/oauth2/debug',
                'cache' => true,
            ),
        ),
    ),
    'api_resource_debug_endpoint_invalid_options' => array(
        'pattern' => '^/api/resource/debug_endpoint/invalid_options$',
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
