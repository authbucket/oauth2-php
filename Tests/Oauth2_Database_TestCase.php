<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests;

use Doctrine\Common\Persistence\PersistentObject;
use Pantarei\Oauth2\Tests\Entity\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

/**
 * Extend PHPUnit_Framework_TestCase for test case require database setup.
 */
class Oauth2_Database_TestCase extends \PHPUnit_Framework_TestCase
{
  protected $em;

  protected $tool;

  public function setUp()
  {
    // Create a simple "default" Doctrine ORM configuration for Annotations.
    $isDevMode = TRUE;
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Entity"), $isDevMode);

    // Database configuration parameters.
    $conn = array(
      'driver' => 'pdo_sqlite',
      'memory' => TRUE,
    );

    // Obtaining the entity manager.
    $this->em = EntityManager::create($conn, $config);
    PersistentObject::setObjectManager($this->em);

    // Generate testing database schema.
    $this->tool = new SchemaTool($this->em);
    $classes = array(
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\AccessTokens'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Authorizes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Clients'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Codes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\RefreshTokens'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Scopes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Users'),
    );
    $this->tool->createSchema($classes);
  }

  public function tearDown()
  {
    // Drop testing database schema.
    $classes = array(
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\AccessTokens'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Authorizes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Clients'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Codes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\RefreshTokens'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Scopes'),
      $this->em->getClassMetadata('Pantarei\Oauth2\Tests\Entity\Users'),
    );
    $this->tool->dropSchema($classes);

    unset($this->tool);
    unset($this->em);
  }
}
