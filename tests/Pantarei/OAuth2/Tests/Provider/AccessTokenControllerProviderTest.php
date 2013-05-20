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

use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\Entity\Codes;
use Pantarei\OAuth2\Extension\GrantType;
use Pantarei\OAuth2\OAuth2WebTestCase;
use Pantarei\OAuth2\Provider\AccessTokenControllerProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenControllerProviderTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->mount('/', new AccessTokenControllerProvider());

    return $app;
  }

  public function testExceptionNoGrantType()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array();
    $server = array();
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionBadGrantType()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'foo',
    );
    $server = array();
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeNoClientId()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array();
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeBothClientId()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
      'client_id' => 'http://democlient1.com/',
      'client_secret' => 'demosecret1',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeBadBasicClientId()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://badclient1.com/',
      'PHP_AUTH_PW' => 'badsecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeBadPostClientId()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
      'client_id' => 'http://badclient1.com/',
      'client_secret' => 'badsecret1',
    );
    $server = array();
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeNoSavedNoPassedRedirectUri()
  {
    // Insert client without redirect_uri.
    $client = new Clients();
    $client->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($client);
    $this->app['oauth2.orm']->flush();

    $code = new Codes();
    $code->setCode('08fb55e26c84f8cb060b7803bc177af8')
      ->setClientId('http://democlient4.com/')
      ->setRedirectUri('')
      ->setExpires(time() + 3600)
      ->setUsername('demousername4')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['oauth2.orm']->persist($code);
    $this->app['oauth2.orm']->flush();

    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => '08fb55e26c84f8cb060b7803bc177af8',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient4.com/',
      'PHP_AUTH_PW' => 'demosecret4',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testExceptionAuthCodeBadRedirectUri()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/wrong_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorAuthCodeNoCode()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $request = new Request();
    $post = array(
      'grant_type' => 'authorization_code',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorClientCredBadState()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'client_credentials',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorClientCredBadStateFormat()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'client_credentials',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorPasswordNoUsername()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'password',
      'password' => 'demopassword1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorPasswordNoPassword()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorPasswordBadScope()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorPasswordBadScopeFormat()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'password',
      'username' => 'demousername1',
      'password' => 'demopassword1',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorRefreshTokenNoToken()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'refresh_token',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorRefreshTokenBadScope()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => "badscope1",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient3.com/',
      'PHP_AUTH_PW' => 'demosecret3',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testErrorRefreshTokenBadScopeFormat()
  {
    $app = new Application;
    $app['debug'] = TRUE;
    $app->mount('/', new AccessTokenControllerProvider());

    $post = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $request = Request::create('/', 'POST', $post, array(), array(), $server);
    $response = $app->handle($request);
    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testGoodAuthCode()
  {
    $parameters = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient2.com/',
      'PHP_AUTH_PW' => 'demosecret2',
    );
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));

    $parameters = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'redirect_uri' => 'http://democlient2.com/redirect_uri',
      'client_id' => 'http://democlient2.com/',
      'client_secret' => 'demosecret2',
    );
    $server = array();
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));
  }

  public function testGoodAuthCodeNoPassedRedirectUri()
  {
    $parameters = array(
      'grant_type' => 'authorization_code',
      'code' => 'f0c68d250bcc729eb780a235371a9a55',
      'client_id' => 'http://democlient2.com/',
      'client_secret' => 'demosecret2',
    );
    $server = array();
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));
  }

  public function testGoodAuthCodeNoStoredRedirectUri()
  {
    // Insert client without redirect_uri.
    $fixture = new Clients();
    $fixture->setClientId('http://democlient4.com/')
      ->setClientSecret('demosecret4')
      ->setRedirectUri('');
    $this->app['oauth2.orm']->persist($fixture);
    $this->app['oauth2.orm']->flush();

    $fixture = new Codes();
    $fixture->setCode('08fb55e26c84f8cb060b7803bc177af8')
      ->setClientId('http://democlient4.com/')
      ->setRedirectUri('')
      ->setExpires(time() + 3600)
      ->setUsername('demousername4')
      ->setScope(array(
        'demoscope1',
      ));
    $this->app['oauth2.orm']->persist($fixture);
    $this->app['oauth2.orm']->flush();

    $parameters = array(
      'grant_type' => 'authorization_code',
      'code' => '08fb55e26c84f8cb060b7803bc177af8',
      'redirect_uri' => 'http://democlient4.com/redirect_uri',
      'client_id' => 'http://democlient4.com/',
      'client_secret' => 'demosecret4',
    );
    $server = array();
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));
  }

  public function testGoodClientCred()
  {
    $parameters = array(
      'grant_type' => 'client_credentials',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient1.com/',
      'PHP_AUTH_PW' => 'demosecret1',
    );
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));
  }

  public function testGoodPassword()
  {
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
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertNotNull(json_decode($client->getResponse()->getContent()));
  }

  public function testGoodRefreshToken()
  {
    $parameters = array(
      'grant_type' => 'refresh_token',
      'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
      'scope' => 'demoscope1 demoscope2 demoscope3',
    );
    $server = array(
      'PHP_AUTH_USER' => 'http://democlient3.com/',
      'PHP_AUTH_PW' => 'demosecret3',
    );
    $client = $this->createClient();
    $crawler = $client->request('POST', '/', $parameters, array(), $server);
    $this->assertTrue($client->getResponse()->isOk());
  }
}
