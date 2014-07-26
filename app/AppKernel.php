<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Provider\AuthBucketOAuth2ServiceProvider;
use AuthBucket\OAuth2\Tests\TestBundle\TestBundleServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app->register(new AuthBucketOAuth2ServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new TestBundleServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new UrlGeneratorServiceProvider());

require __DIR__ . '/config/config_' . $app['env'] . '.php';
require __DIR__ . '/config/routing_' . $app['env'] . '.php';

$app->before(function (Request $request) use ($app) {
    $app['session']->start();
});
