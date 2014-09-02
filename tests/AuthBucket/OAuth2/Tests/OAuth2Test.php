<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * Overall OAuth2 workflow test cases.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class OAuth2Test extends WebTestCase
{
    public function testAuthorizationCodeGrant()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Query authorization endpoint with response_type = code.
        $parameters = array(
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        // Check basic auth response that can simply compare.
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $this->assertEquals(
            'http://democlient1.com/redirect_uri',
            $authResponse->getSchemeAndHttpHost().$authResponse->getBaseUrl().$authResponse->getPathInfo()
        );

        // Query token endpoint with grant_type = authorization_code.
        $codeResponse = $authResponse->query->all();
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => $codeResponse['code'],
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
            'state' => $codeResponse['state'],
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $tokenResponse['token_type']);
        $this->assertEquals('demoscope1', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $debugResponse['username']);
    }

    public function testImplicitGrant()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Query authorization endpoint with response_type = token.
        $parameters = array(
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        );
        $server = array(
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/authorize', $parameters, array(), $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        // Check basic auth response that can simply compare.
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $this->assertEquals(
            'http://democlient1.com/redirect_uri',
            $authResponse->getSchemeAndHttpHost().$authResponse->getBaseUrl().$authResponse->getPathInfo()
        );

        // Check basic token response that can simply compare.
        $tokenResponse = $authResponse->query->all();
        $this->assertEquals('bearer', $tokenResponse['token_type']);
        $this->assertEquals('demoscope1', $tokenResponse['scope']);
        $this->assertEquals($session->getId(), $tokenResponse['state']);

        // Query debug endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $debugResponse['username']);
    }

    public function testResourceOwnerPasswordCredentialsGrant()
    {
        // Query the token endpoint with grant_type = password.
        $parameters = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $tokenResponse['token_type']);
        $this->assertEquals('demoscope1', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('demousername1', $debugResponse['username']);
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
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/v1.0/oauth2/token', $parameters, array(), $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('bearer', $tokenResponse['token_type']);
        $this->assertEquals('demoscope1 demoscope2 demoscope3', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = array();
        $server = array(
            'HTTP_Authorization' => implode(' ', array('Bearer', $tokenResponse['access_token'])),
        );
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/v1.0/oauth2/debug', $parameters, array(), $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('', $debugResponse['username']);
    }
}
