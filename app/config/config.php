<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Tests\TestBundle\Entity\ModelManagerFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

require __DIR__ . '/security.php';

$app['debug'] = true;

$app['twig.path'] = array(
    __DIR__ . '/../../tests/src/AuthBucket/OAuth2/Tests/TestBundle/Resources/views',
);

// Fake lib dev, simply use plain text encoder.
$app['security.encoder.digest'] = $app->share(function ($app) {
    return new PlaintextPasswordEncoder();
});

// Define SQLite DB path.
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../cache/' . $app['env'] . '/.ht.sqlite',
);

// Return an instance of Doctrine ORM entity manager.
$app['authbucket_oauth2.orm'] = $app->share(function ($app) {
    $conn = $app['dbs']['default'];
    $em = $app['dbs.event_manager']['default'];

    $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__ . '/../../tests/src/AuthBucket/OAuth2/Tests/TestBundle/Entity'));

    $config = Setup::createConfiguration(false);
    $config->setMetadataDriverImpl($driver);
    $config->setMetadataCacheImpl(new ArrayCache());
    $config->setQueryCacheImpl(new ArrayCache());

    return EntityManager::create($conn, $config, $em);
});

// Return entity classes for model manager.
$app['authbucket_oauth2.model'] = array(
    'access_token' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\AccessToken',
    'authorize' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Authorize',
    'client' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Client',
    'code' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Code',
    'refresh_token' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\RefreshToken',
    'scope' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Scope',
);

// Add model managers from ORM.
$app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
    return new ModelManagerFactory($app['authbucket_oauth2.orm'], $app['authbucket_oauth2.model']);
});

// Define endpoint path.
$app['authbucket_oauth2.authorize_path'] = '/oauth2/authorize';
$app['authbucket_oauth2.token_path'] = '/oauth2/token';
$app['authbucket_oauth2.debug_path'] = '/oauth2/debug';
$app['authbucket_oauth2.authorize_scope_path'] = '/oauth2/authorize/scope';

// We simply reuse the user provider that already created for authorize firewall
// here.
$app['authbucket_oauth2.user_provider'] = $app['security.user_provider.default'];
