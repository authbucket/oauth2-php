<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests;

use Pantarei\OAuth2\Database\Database;
use Pantarei\OAuth2\Tests\Entity\AccessTokens;
use Pantarei\OAuth2\Tests\Entity\Authorizes;
use Pantarei\OAuth2\Tests\Entity\Clients;
use Pantarei\OAuth2\Tests\Entity\Codes;
use Pantarei\OAuth2\Tests\Entity\RefreshTokens;
use Pantarei\OAuth2\Tests\Entity\Scopes;
use Pantarei\OAuth2\Tests\Entity\Users;

/**
 * Extend PHPUnit_Framework_TestCase for test case require database setup.
 */
class OAuth2_Database_TestCase extends \PHPUnit_Framework_TestCase
{
  protected $em;

  protected $tool;

  public function setUp()
  {
    $this->em = Database::getConnection()->em;
    $this->tool = Database::getConnection()->tool;

    // Add tables and sample data.
    $this->addTables();
    $this->addSampleData();
  }

  public function tearDown()
  {
    // Drop tables and reset connection settings.
    $this->dropTables();
    Database::closeConnection();
  }

  function addTables()
  {
    // Generate testing database schema.
    $classes = array(
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\AccessTokens'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Authorizes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Clients'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Codes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\RefreshTokens'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Scopes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Users'),
    );
    $this->tool->createSchema($classes);

  }

  function dropTables()
  {
    // Drop testing database schema.
    $classes = array(
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\AccessTokens'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Authorizes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Clients'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Codes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\RefreshTokens'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Scopes'),
      $this->em->getClassMetadata('Pantarei\OAuth2\Tests\Entity\Users'),
    );
    $this->tool->dropSchema($classes);
  }

  function addSampleData()
  {
    // Add demo access token.
    $accessToken = new AccessTokens();
    $accessToken->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
      ->setClientId('http://democlient1.com/')
      ->setExpiresIn('3600')
      ->setUsername('demouser1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->em->persist($accessToken);

    // Add demo authorizes.
    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient1.com/')
      ->setUsername('demouser1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->em->persist($authorize);

    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient2.com/')
      ->setUsername('demouser2')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
      ));
    $this->em->persist($authorize);

    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient3.com/')
      ->setUsername('demouser3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->em->persist($authorize);

    // Add demo clients.
    $client = new Clients();
    $client->setClientId('http://democlient1.com/')
      ->setClientSecret('demosecret1')
      ->setRedirectUri('http://democlient1.com/redirect');
    $this->em->persist($client);

    $client = new Clients();
    $client->setClientId('http://democlient2.com/')
      ->setClientSecret('demosecret2')
      ->setRedirectUri('http://democlient2.com/redirect');
    $this->em->persist($client);

    $client = new Clients();
    $client->setClientId('http://democlient3.com/')
      ->setClientSecret('demosecret3')
      ->setRedirectUri('http://democlient3.com/redirect');
    $this->em->persist($client);

    // Add demo code.
    $code = new Codes();
    $code->setCode('f0c68d250bcc729eb780a235371a9a55')
      ->setClientId('http://democlient2.com/')
      ->setRedirectUri('http://democlient2.com/redirect')
      ->setExpiresIn('300')
      ->setUsername('demouser2')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
      ));
    $this->em->persist($code);

    // Add demo refresh token.
    $refreshToken = new RefreshTokens();
    $refreshToken->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
      ->setClientId('http://democlient3.com/')
      ->setExpiresIn('86400')
      ->setUsername('demouser3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->em->persist($refreshToken);

    // Add demo scopes.
    $scope = new Scopes();
    $scope->setScope('demoscope1');
    $this->em->persist($scope);

    $scope = new Scopes();
    $scope->setScope('demoscope2');
    $this->em->persist($scope);

    $scope = new Scopes();
    $scope->setScope('demoscope3');
    $this->em->persist($scope);

    // Add demo users.
    $user = new Users();
    $user->setUsername('demouser1')
      ->setPassword('demopassword1');
    $this->em->persist($user);

    $user = new Users();
    $user->setUsername('demouser2')
      ->setPassword('demopassword2');
    $this->em->persist($user);

    $user = new Users();
    $user->setUsername('demouser3')
      ->setPassword('demopassword3');
    $this->em->persist($user);

    // Now flush all presisted demo data to sqlite3.
    $this->em->flush();
  }
}
