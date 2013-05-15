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
    $app['oauth2.orm.default_options'] = array(
      'connection' => 'default',
      'path' => __DIR__ . '/Entity',
    );

    $app['oauth2.orms.options.initializer'] = $app->protect(function () use ($app) {
      static $initialized = FALSE;

      if ($initialized) {
        return;
      }
      $initialized = TRUE;

      if (!isset($app['oauth2.orms.options'])) {
        $app['oauth2.orms.options'] = array(
          'default' => isset($app['oauth2.orm.options']) ? $app['oauth2.orm.options'] : array(),
        );
      }

      $tmp = $app['oauth2.orms.options'];
      foreach ($tmp as $name => &$options) {
        $options = array_replace($app['oauth2.orm.default_options'], $options);
        if (!isset($app['oauth2.orms.default'])) {
          $app['oauth2.orms.default'] = $name;
        }
      }
      $app['oauth2.orms.options'] = $tmp;
    });

    $app['oauth2.orms'] = $app->share(function ($app) {
      $app['oauth2.orms.options.initializer']();

      $orms = new \Pimple();
      foreach ($app['oauth2.orms.options'] as $name => $options) {
        $conn = $app['dbs'][$options['connection']];
        $event_manager = $app['dbs.event_manager'][$options['connection']];
        // We use shortcuts here in case the default has been overridden.
        if ($app['oauth2.orms.default'] === $name) {
          $config = $app['oauth2.orm.config'];
        }
        else {
          $config = $app['oauth2.orms.config'][$name];
        }
        $orms[$name] = EntityManager::create($conn, $config, $event_manager);
      }
      return $orms;
    });

    $app['oauth2.orms.config'] = $app->share(function($app) {
      $app['oauth2.orms.options.initializer']();

      $configs = new \Pimple();
      foreach ($app['oauth2.orms.options'] as $name => $options) {
        $configs[$name] = Setup::createAnnotationMetadataConfiguration(array($options['path']), TRUE);
      }
      return $configs;
    });

    // Shortcurs for the "first" ORM.
    $app['oauth2.orm'] = $app->share(function ($app) {
      $orms = $app['oauth2.orms'];
      return $orms[$app['oauth2.orms.default']];
    });

    $app['oauth2.orm.config'] = $app->share(function ($app) {
      $orms = $app['oauth2.orms.config'];
      return $orms[$app['oauth2.orms.default']];
    });
  }

  public function boot(Application $app)
  {
  }
}
