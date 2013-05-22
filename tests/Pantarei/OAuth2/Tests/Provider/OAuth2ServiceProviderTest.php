<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Provider;

use Pantarei\OAuth2\Provider\DoctrineORMServiceProvider;
use Pantarei\OAuth2\OAuth2WebTestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

/**
 * Test base OAuth2.0 exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class DoctrineORMServiceProviderTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->register(new DoctrineORMServiceProvider, array(
      'oauth2.orms.options' => array(
        'master' => array(
          'connection' => 'default',
          'path' => __DIR__ . '/Entity',
        ),
        'slave' => array(
          'connection' => 'default',
          'path' => __DIR__ . '/Entity',
        ),
      ),
    ));

    return $app;
  }

  public function testFind()
  {
    $result = $this->app['oauth2.orm']->find($app['oauth2.entity']['AccessTokens'], 1);
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
  }

  public function testFindOnSlave()
  {
    $result = $this->app['oauth2.orms']['slave']->find($app['oauth2.entity']['AccessTokens'], 1);
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
  }

  public function testFindBy()
  {
    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['AccessTokens'])->findBy(array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result[0]->getAccessToken());
  }

  public function testFindOneBy()
  {
    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['AccessTokens'])->findOneBy(array(
      'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
    ));
    $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
  }

  public function testFindAll()
  {
    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['Clients'])->findAll();
    $this->assertEquals(3, count($result));
  }

  public function testPersist()
  {
    $data = new Scopes();
    $data->setScope('demoscope4');
    $this->app['oauth2.orm']->persist($data);
    $this->app['oauth2.orm']->flush();

    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['Scopes'])->findAll();
    $this->assertEquals(4, count($result));
    $this->assertEquals('demoscope4', $result[3]->getScope());
  }

  public function testRemove()
  {
    $data = new Scopes();
    $data->setScope('demoscope4');
    $this->app['oauth2.orm']->persist($data);
    $this->app['oauth2.orm']->flush();

    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['Scopes'])->findAll();
    $this->assertEquals(4, count($result));
    $this->assertEquals('demoscope4', $result[3]->getScope());

    $this->app['oauth2.orm']->remove($data);
    $this->app['oauth2.orm']->flush();
    $result = $this->app['oauth2.orm']->getRepository($app['oauth2.entity']['Scopes'])->findAll();
    $this->assertEquals(3, count($result));
  }
}
