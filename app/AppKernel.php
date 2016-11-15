<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

// Register MUST have Silex providers for AuthBucketOAuth2ServiceProvider.
$app->register(new Silex\Provider\MonologServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Register AuthBucketOAuth2ServiceProvider.
$app->register(new AuthBucket\OAuth2\Provider\AuthBucketOAuth2ServiceProvider());

// Register additional Silex providers for TestBundleServiceProvider.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\RememberMeServiceProvider());
$app->register(new Silex\Provider\RoutingServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider());

// Register TestBundleServiceProvider.
$app->register(new AuthBucket\OAuth2\Tests\TestBundle\TestBundleServiceProvider());

require __DIR__.'/config/config_'.$app['env'].'.php';

$app->before(function (Request $request) use ($app) {
    $app['session']->start();
});
