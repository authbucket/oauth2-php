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

use Pantarei\OAuth2\WebTestCase;
use Pantarei\OAuth2\Provider\OAuth2ControllerProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Overall OAuth2 workflow test cases.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2Test extends WebTestCase
{
    public function testAuthorizationCodeGrant()
    {
        // Query authorization endpoint with response_type = code.
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        // Check basic auth response that can simply compare.
        $auth_response = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $this->assertEquals(
            'http://democlient1.com/redirect_uri',
            $auth_response->getSchemeAndHttpHost() . $auth_response->getBaseUrl() . $auth_response->getPathInfo()
        );
        $this->assertEquals('example state', $auth_response->query->get('state'));

        // Check code with database record.
        $code = $auth_response->query->get('code');
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.codes'])
            ->findOneBy(array(
                'client_id' => 'http://democlient1.com/',
                'code' => $code,
            ));
        $this->assertNotNull($result);

        // Query token endpoint with grant_type = authorization_code.
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Check basic token response that can simply compare.
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $token_response['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $token_response['scope']);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query resource endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $token_response['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testImplicitGrant()
    {
        // Query authorization endpoint with response_type = token.
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Check basic token response that can simply compare.
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $token_response['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $token_response['scope']);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query resource endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $token_response['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testResourceOwnerPasswordCredentialsGrant()
    {
        // Query the token endpoint with grant_type = password.
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'demostate1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Check basic token response that can simply compare.
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $token_response['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $token_response['scope']);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query resource endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $token_response['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testClientCredentialsGrant()
    {
        // Query the token endpoint with grant_type = client_credentials.
        $parameters = array(
            'grant_type' => 'client_credentials',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Check basic token response that can simply compare.
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $token_response['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $token_response['scope']);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query resource endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $token_response['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }

    public function testRefreshingAccessToken()
    {
        // Query authorization endpoint with response_type = token.
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1 demoscope2 demoscope3',
            'state' => 'example state',
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/authorize', $parameters, array(), $server);
        $token_response = json_decode($client->getResponse()->getContent(), true);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query token endpoint with grant_type = refresh_token.
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $token_response['refresh_token'],
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);

        // Check basic token response that can simply compare.
        $token_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $token_response['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $token_response['scope']);

        // Check access token and refresh token with database record.
        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.access_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'access_token' => $token_response['access_token'],
            ));
        $this->assertNotNull($result);

        $result = $this->app['oauth2.orm']
            ->getRepository($this->app['oauth2.entity.refresh_tokens'])
            ->findOneBy(array(
                'token_type' => 'bearer',
                'client_id' => 'http://democlient1.com/',
                'refresh_token' => $token_response['refresh_token'],
            ));
        $this->assertNotNull($result);

        // Query resource endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $token_response['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/resource/foo', $parameters, array(), $server);
        $this->assertEquals('foo', $client->getResponse()->getContent());
    }
}
