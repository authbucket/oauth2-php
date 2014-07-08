<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/routing_client.php';
require __DIR__ . '/routing_demo.php';
require __DIR__ . '/routing_oauth2.php';
require __DIR__ . '/routing_resource.php';

// Index.
$app->get('/', function (Request $request) use ($app) {
    return $app['twig']->render('index.html.twig');
})->bind('index');

// Admin, flush database.
$app->get('/admin/refresh_database', function (Request $request) use ($app) {
    $connection = $app['db'];
    $em = $app['authbucket_oauth2.orm'];

    $params = $connection->getParams();
    $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

    try {
        $connection->getSchemaManager()->dropDatabase($name);
        $connection->getSchemaManager()->createDatabase($name);
        $connection->close();
    } catch (\Exception $e) {
        return 1;
    }

    $classes = array();
    foreach ($app['authbucket_oauth2.model'] as $class) {
        $classes[] = $em->getClassMetadata($class);
    }

    PersistentObject::setObjectManager($em);
    $tool = new SchemaTool($em);
    $tool->dropSchema($classes);
    $tool->createSchema($classes);

    $purger = new ORMPurger();
    $executor = new ORMExecutor($em, $purger);

    $loader = new Loader();
    $loader->loadFromDirectory(__DIR__ . '/../../DataFixtures/ORM');
    $executor->execute($loader->getFixtures());

    return $app->redirect($app['url_generator']->generate('index'));
})->bind('admin_refresh_database');
