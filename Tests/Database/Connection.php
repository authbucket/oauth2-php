<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Database;

use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Pantarei\OAuth2\Database\Connection as DatabaseConnection;

/**
 * Extend connection class for doctrine.
 */
class Connection extends DatabaseConnection
{
  public $em;

  public $tool;

  public function __construct(array $connection_options = array())
  {
    // Create a simple "default" Doctrine ORM configuration for Annotations.
    $isDevMode = TRUE;
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../Entity"), $isDevMode);

    // Database configuration parameters.
    $conn = array(
      'driver' => 'pdo_sqlite',
      'memory' => TRUE,
    );

    // Obtaining the entity manager.
    $this->em = EntityManager::create($conn, $config);
    PersistentObject::setObjectManager($this->em);
    $this->tool = new SchemaTool($this->em);
  }

  public function __destruct()
  {
    // Drop tables and reset connection settings.
    unset($this->tool);
    unset($this->em);
  }
}
