<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * A simple Doctrine ORM service provider for OAuth2.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DoctrineORMServiceProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {
    $app['orm.default_options'] = array(
      'connection' => 'default',
      'path' => __DIR__ . '/Entity',
    );

    $app['orms.options.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return;
      }
      $initialized = TRUE;

      if (!isset($app['orms.options'])) {
        $app['orms.options'] = array(
          'default' => isset($app['orm.options']) ? $app['orm.options'] : array(),
        );
      }

      $tmp = $app['orms.options'];
      foreach ($tmp as $name => &$options) {
        $options = array_replace($app['orm.default_options'], $options);
        if (!isset($app['orms.default'])) {
          $app['orms.default'] = $name;
        }
      }
      $app['orms.options'] = $tmp;
    });

    $app['orms'] = $app->share(function ($app) {
      $app['orms.options.initializer']();

      $orms = new \Pimple();
      foreach ($app['orms.options'] as $name => $options) {
        $conn = $app['dbs'][$options['connection']];
        $event_manager = $app['dbs.event_manager'][$options['connection']];
        // We use shortcuts here in case the default has been overridden.
        if ($app['orms.default'] === $name) {
          $config = $app['orm.config'];
        }
        else {
          $config = $app['orms.config'][$name];
        }
        $orms[$name] = EntityManager::create($conn, $config, $event_manager);
      }
      return $orms;
    });

    $app['orms.config'] = $app->share(function($app) {
      $app['orms.options.initializer']();

      $configs = new \Pimple();
      foreach ($app['orms.options'] as $name => $options) {
        $configs[$name] = Setup::createAnnotationMetadataConfiguration(array($options['path']), TRUE);
      }
      return $configs;
    });

    // Shortcurs for the "first" ORM.
    $app['orm'] = $app->share(function ($app) {
      $orms = $app['orms'];
      return $orms[$app['orms.default']];
    });

    $app['orm.config'] = $app->share(function ($app) {
      $orms = $app['orms.config'];
      return $orms[$app['orms.default']];
    });
  }

  public function boot(Application $app)
  {
  }
}
