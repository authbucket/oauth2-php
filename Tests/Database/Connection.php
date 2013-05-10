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
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Pantarei\OAuth2\Database\Connection as DatabaseConnection;

/**
 * Extend connection class for doctrine.
 */
class Connection extends DatabaseConnection
{
  private $em;

  private $tool;

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

  /**
   * Return the stored EntityManager for debug only.
   *
   * FIXME: Don't do this in production bundle.
   *
   * @return
   *   The stored EntityManager.
   */
  public function getEntityManager()
  {
    return $this->em;
  }

  /**
   * Return the stored SchemaTool for debug only.
   *
   * FIXME: Don't do this in production bundle.
   *
   * @return
   *   The stored SchemaTool.
   */
  public function getSchemaTool()
  {
    return $this->tool;
  }

  public function find($entityName, $id)
  {
    return $this->em->find($entityName, $id);
  }

  public function findBy($entityName, $criteria)
  {
    return $this->em->getRepository($entityName)->findBy($criteria);
  }

  public function findOneBy($entityName, $criteria)
  {
    return $this->em->getRepository($entityName)->findOneBy($criteria);
  }

  public function findAll($entityName)
  {
    return $this->em->getRepository($entityName)->findAll();
  }

  public function persist($entity)
  {
    $this->em->persist($entity);
    $this->em->flush();
  }

  public function remove($entity)
  {
    $this->em->remove($entity);
    $this->em->flush();
  }
}
