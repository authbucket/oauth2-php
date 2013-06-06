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

use Pantarei\OAuth2\Provider\OAuth2ControllerProvider;
use Pantarei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test resource endpoint functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResourceEndpointTest extends WebTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        $app->mount('/', new OAuth2ControllerProvider());

        return $app;
    }

    public function testAuthorizationHeader()
    {
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => 'Bearer eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }
    
    public function testGet()
    {
        $parameters = array(
            'access_token' => 'eeb5aa92bbb4b56373b9e0d00bc02d93',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }
}
