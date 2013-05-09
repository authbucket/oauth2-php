<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Test\Database;

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Tests\Database\Connection;

/**
 * Test base OAuth2.0 exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
  public function testDatabaseInfo()
  {
    $databaseInfo = array(
      'namespace' => 'Pantarei\\OAuth2\\Tests\\Database',
    );

    Database::setDatabaseInfo($databaseInfo);
    $this->assertEquals($databaseInfo, Database::getDatabaseInfo());
  }

  public function testConnection()
  {
    $databaseInfo = array(
      'namespace' => 'Pantarei\\OAuth2\\Tests\\Database',
    );

    Database::setDatabaseInfo($databaseInfo);

    $conn = Database::getConnection();
    $this->assertTrue($conn instanceof \Pantarei\OAuth2\Database\Connection);
  }
}
