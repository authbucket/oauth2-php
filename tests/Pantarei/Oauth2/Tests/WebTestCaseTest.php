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

use Pantarei\Oauth2\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the WebTestCase wrapper functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class WebTestCaseTest extends WebTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        $app->get('/foo/{name}', function ($name, Request $request) {
            $request->overrideGlobals();
            $controller = new WebTestCaseTest();
            return $controller->bar($name);
        });

        return $app;
    }

    public function bar($name)
    {
        return 'Hello ' . $name;
    }

    public function testSilexPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/foo/bar', array('dummy' => 'content'));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Hello bar")'));
    }
}
