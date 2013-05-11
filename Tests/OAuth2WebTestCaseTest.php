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

use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test the OAuth2WebTestCase wrapper functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2WebTestCaseTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->get('/foo/{name}', function ($name) {
      $controller = new self();
      return $controller->bar($name);
    });

    return $app;
  }

  public function bar($name) {
    return 'Hello ' . $name;
  }

  public function testSilexPage()
  {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/foo/bar');
    $this->assertTrue($client->getResponse()->isOk());
    $this->assertCount(1, $crawler->filter('html:contains("Hello bar")'));
  }
}
