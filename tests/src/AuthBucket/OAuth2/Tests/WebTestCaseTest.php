<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests;

use AuthBucket\OAuth2\Tests\WebTestCase;
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
        $parameters = array();
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/foo/bar', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Hello bar")'));
    }
}
