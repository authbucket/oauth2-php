<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Tests\TestBundle\Entity\ModelManagerFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

// Define SQLite DB path.
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__.'/../cache/'.$app['env'].'/.ht.sqlite',
);

// Return an instance of Doctrine ORM entity manager.
$app['doctrine.orm.entity_manager'] = $app->share(function ($app) {
    $conn = $app['dbs']['default'];
    $em = $app['dbs.event_manager']['default'];

    $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__.'/../../tests/AuthBucket/OAuth2/Tests/TestBundle/Entity'));
    $cache = new FilesystemCache(__DIR__.'/../cache/'.$app['env']);

    $config = Setup::createConfiguration(false);
    $config->setMetadataDriverImpl($driver);
    $config->setMetadataCacheImpl($cache);
    $config->setQueryCacheImpl($cache);

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
    'user' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\User',
);

// Add model managers from ORM.
$app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
    return new ModelManagerFactory(
        $app['doctrine.orm.entity_manager'],
        $app['authbucket_oauth2.model']
    );
});

require __DIR__.'/routing.php';
require __DIR__.'/security.php';

$app['debug'] = true;

$app['twig.path'] = array(
    __DIR__.'/../../tests/AuthBucket/OAuth2/Tests/TestBundle/Resources/views',
);

// We simply reuse the user provider that already created for authorize firewall
// here.
$app['authbucket_oauth2.user_provider'] = $app['security.user_provider.default'];
