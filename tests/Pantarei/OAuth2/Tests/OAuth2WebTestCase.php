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

use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\Tools\SchemaTool;
use Pantarei\OAuth2\Entity\AccessTokens;
use Pantarei\OAuth2\Entity\Authorizes;
use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Entity\Codes;
use Pantarei\OAuth2\Entity\RefreshTokens;
use Pantarei\OAuth2\Entity\Scopes;
use Pantarei\OAuth2\Entity\Users;
use Pantarei\OAuth2\Provider\DoctrineORMServiceProvider;
use Pantarei\OAuth2\Provider\ParameterServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\WebTestCase;

/**
 * Extend Silex\WebTestCase for test case require database and web interface
 * setup.
 */
class OAuth2WebTestCase extends WebTestCase
{
  public function createApplication()
  {
    $app = new Application();
    $app['debug'] = TRUE;
    $app['session'] = TRUE;
    $app['exception_handler']->disable();

    $app->register(new DoctrineServiceProvider, array(
      'db.options' => array(
        'driver' => 'pdo_sqlite',
        'memory' => TRUE,
      ),
    ));
    $app->register(new DoctrineORMServiceProvider, array(
      'orm.options' => array(
        'connection' => 'default',
        'path' => __DIR__ . '/Entity',
      ),
    ));
    $app->register(new ParameterServiceProvider());

    return $app;
  }

  public function setUp()
  {
    // Initialize with parent's setUp().
    parent::setUp();

    // Add tables and sample data.
    $this->createSchema();
    $this->addSampleData();
  }

  public function tearDown()
  {
    // Drop tables and reset connection settings.
    $this->dropSchema();

    // Finalize with parent's tearDown().
    parent::tearDown();
  }

  function createSchema()
  {
    // Generate testing database schema.
    $classes = array(
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\AccessTokens'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Authorizes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Clients'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Codes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\RefreshTokens'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Scopes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Users'),
    );

    PersistentObject::setObjectManager($this->app['orm']);
    $tool = new SchemaTool($this->app['orm']);
    $tool->createSchema($classes);
  }

  function dropSchema()
  {
    // Drop testing database schema.
    $classes = array(
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\AccessTokens'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Authorizes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Clients'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Codes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\RefreshTokens'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Scopes'),
      $this->app['orm']->getClassMetadata('Pantarei\OAuth2\Entity\Users'),
    );

    PersistentObject::setObjectManager($this->app['orm']);
    $tool = new SchemaTool($this->app['orm']);
    $tool->dropSchema($classes);
  }

  function addSampleData()
  {
    // Add demo access token.
    $access_token = new AccessTokens();
    $access_token->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
      ->setClientId('http://democlient1.com/')
      ->setExpires(time() + 28800)
      ->setUsername('demousername1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['orm']->persist($access_token);

    // Add demo authorizes.
    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient1.com/')
      ->setUsername('demousername1')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['orm']->persist($authorize);

    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient2.com/')
      ->setUsername('demousername2')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
      ));
    $this->app['orm']->persist($authorize);

    $authorize = new Authorizes();
    $authorize->setClientId('http://democlient3.com/')
      ->setUsername('demousername3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->app['orm']->persist($authorize);

    // Add demo clients.
    $client = new Clients();
    $client->setClientId('http://democlient1.com/')
      ->setClientSecret('demosecret1')
      ->setRedirectUri('http://democlient1.com/redirect_uri');
    $this->app['orm']->persist($client);

    $client = new Clients();
    $client->setClientId('http://democlient2.com/')
      ->setClientSecret('demosecret2')
      ->setRedirectUri('http://democlient2.com/redirect_uri');
    $this->app['orm']->persist($client);

    $client = new Clients();
    $client->setClientId('http://democlient3.com/')
      ->setClientSecret('demosecret3')
      ->setRedirectUri('http://democlient3.com/redirect_uri');
    $this->app['orm']->persist($client);

    // Add demo code.
    $code = new Codes();
    $code->setCode('f0c68d250bcc729eb780a235371a9a55')
      ->setClientId('http://democlient2.com/')
      ->setRedirectUri('http://democlient2.com/redirect_uri')
      ->setExpires(time() + 3600)
      ->setUsername('demousername2')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
      ));
    $this->app['orm']->persist($code);

    // Add demo refresh token.
    $refresh_token = new RefreshTokens();
    $refresh_token->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
      ->setClientId('http://democlient3.com/')
      ->setExpires(time() + 86400)
      ->setUsername('demousername3')
      ->setScope(array(
        'demoscope1',
        'demoscope2',
        'demoscope3',
      ));
    $this->app['orm']->persist($refresh_token);

    // Add demo scopes.
    $scope = new Scopes();
    $scope->setScope('demoscope1');
    $this->app['orm']->persist($scope);

    $scope = new Scopes();
    $scope->setScope('demoscope2');
    $this->app['orm']->persist($scope);

    $scope = new Scopes();
    $scope->setScope('demoscope3');
    $this->app['orm']->persist($scope);

    // Add demo users.
    $user = new Users();
    $user->setUsername('demousername1')
      ->setPassword('demopassword1');
    $this->app['orm']->persist($user);

    $user = new Users();
    $user->setUsername('demousername2')
      ->setPassword('demopassword2');
    $this->app['orm']->persist($user);

    $user = new Users();
    $user->setUsername('demousername3')
      ->setPassword('demopassword3');
    $this->app['orm']->persist($user);

    // Flush all records to database
    $this->app['orm']->flush();
  }
}
