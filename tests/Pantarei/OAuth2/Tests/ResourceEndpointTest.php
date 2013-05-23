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

use Pantarei\OAuth2\OAuth2WebTestCase;
use Pantarei\OAuth2\Provider\OAuth2ControllerProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test resource endpoint functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceEndpointTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->mount('/', new OAuth2ControllerProvider());

    return $app;
  }

  public function testResourceEndpoint()
  {
    $parameters = array();
    $client = $this->createClient();
    $crawler = $client->request('GET', '/resource/foo', $parameters);
    $this->assertEquals('foo', $client->getResponse()->getContent());
  }
}
