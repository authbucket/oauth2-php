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
        $parameters = [
            'response_type' => 'code',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        ];
        $server = [
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/authorize', $parameters, [], $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        // Check basic auth response that can simply compare.
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $this->assertSame(
            'http://democlient1.com/redirect_uri',
            $authResponse->getSchemeAndHttpHost().$authResponse->getBaseUrl().$authResponse->getPathInfo()
        );

        // Query token endpoint with grant_type = authorization_code.
        $codeResponse = $authResponse->query->all();
        $parameters = [
            'grant_type' => 'authorization_code',
            'code' => $codeResponse['code'],
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
            'state' => $codeResponse['state'],
        ];
        $server = [];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('bearer', $tokenResponse['token_type']);
        $this->assertSame('demoscope1', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', $tokenResponse['access_token']]),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $debugResponse['username']);
    }

    public function testImplicitGrant()
    {
        // Start session manually.
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        // Query authorization endpoint with response_type = token.
        $parameters = [
            'response_type' => 'token',
            'client_id' => 'http://democlient1.com/',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        ];
        $server = [
            'PHP_AUTH_USER' => 'demousername1',
            'PHP_AUTH_PW' => 'demopassword1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/authorize', $parameters, [], $server);
        $this->assertTrue($client->getResponse()->isRedirect());

        // Check basic auth response that can simply compare.
        $authResponse = Request::create($client->getResponse()->headers->get('Location'), 'GET');
        $this->assertSame(
            'http://democlient1.com/redirect_uri',
            $authResponse->getSchemeAndHttpHost().$authResponse->getBaseUrl().$authResponse->getPathInfo()
        );

        // Check basic token response that can simply compare.
        $tokenResponse = $authResponse->query->all();
        $this->assertSame('bearer', $tokenResponse['token_type']);
        $this->assertSame('demoscope1', $tokenResponse['scope']);
        $this->assertSame($session->getId(), $tokenResponse['state']);

        // Query debug endpoint with access_token.
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', $tokenResponse['access_token']]),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $debugResponse['username']);
    }

    public function testResourceOwnerPasswordCredentialsGrant()
    {
        // Query the token endpoint with grant_type = password.
        $parameters = [
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope1',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('bearer', $tokenResponse['token_type']);
        $this->assertSame('demoscope1', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', $tokenResponse['access_token']]),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('demousername1', $debugResponse['username']);
    }

    public function testClientCredentialsGrant()
    {
        // Query the token endpoint with grant_type = client_credentials.
        $parameters = [
            'grant_type' => 'client_credentials',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));

        // Query token endpoint with grant_type = refresh_token.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $parameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenResponse['refresh_token'],
            'scope' => 'demoscope1 demoscope2 demoscope3',
        ];
        $server = [
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        ];
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);

        // Check basic token response that can simply compare.
        $tokenResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('bearer', $tokenResponse['token_type']);
        $this->assertSame('demoscope1 demoscope2 demoscope3', $tokenResponse['scope']);

        // Query debug endpoint with access_token.
        $parameters = [];
        $server = [
            'HTTP_Authorization' => implode(' ', ['Bearer', $tokenResponse['access_token']]),
        ];
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/oauth2/debug', $parameters, [], $server);
        $debugResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('', $debugResponse['username']);
    }
}
