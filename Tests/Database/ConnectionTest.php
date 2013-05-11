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

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Tests\Database\Connection;

/**
 * Test base OAuth2.0 exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\Exception
   */
  public function testNoDatabaseInfo()
  {
    $conn = Database::getConnection();
    // This won't happened!!
    $this->assertTrue($conn instanceof \Pantarei\OAuth2\Database\Connection);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\Exception
   */
  public function testNoDatabaseNamespace()
  {
    $databaseInfo = array();
    Database::setDatabaseInfo($databaseInfo);
    $conn = Database::getConnection();

    // This won't happened!!
    $this->assertTrue($conn instanceof \Pantarei\OAuth2\Database\Connection);
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\Exception
   */
  public function testNoEntityNamespace()
  {
    $databaseInfo['Database']['namespace'] = 'Pantarei\\OAuth2\\Tests\\Database';
    Database::setDatabaseInfo($databaseInfo);
    $conn = Database::getConnection();

    // This won't happened!!
    $this->assertTrue($conn instanceof \Pantarei\OAuth2\Database\Connection);
  }

  public function testDatabaseInfo()
  {
    $databaseInfo['Database']['namespace'] = 'Pantarei\\OAuth2\\Tests\\Database';
    $databaseInfo['Entity']['namespace'] = 'Pantarei\\OAuth2\\Tests\\Entity';
    Database::setDatabaseInfo($databaseInfo);

    $this->assertEquals($databaseInfo, Database::getDatabaseInfo());
  }

  public function testConnection()
  {
    $databaseInfo['Database']['namespace'] = 'Pantarei\\OAuth2\\Tests\\Database';
    $databaseInfo['Entity']['namespace'] = 'Pantarei\\OAuth2\\Tests\\Entity';
    Database::setDatabaseInfo($databaseInfo);

    $conn = Database::getConnection();
    $this->assertTrue($conn instanceof \Pantarei\OAuth2\Database\Connection);
  }
}
