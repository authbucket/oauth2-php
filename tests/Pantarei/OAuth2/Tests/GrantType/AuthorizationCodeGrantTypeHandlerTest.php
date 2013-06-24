<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\GrantType;

use Pantarei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationCodeGrantTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeNoClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeBothClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
    public function testExceptionAuthCodeBadBasicClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://badclient1.com/',
            'PHP_AUTH_PW' => 'badsecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
    public function testExceptionAuthCodeBadPostClientId()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://badclient1.com/',
            'client_secret' => 'badsecret1',
        );
        $server = array();
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeNoSavedNoPassedRedirectUri()
    {
        // Insert client without redirect_uri.
        $modelManager =  $this->app['oauth2.model_manager.factory']->getModelManager('client');
        $model = $modelManager->createClient();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4');
        $modelManager->updateClient($model);

        $modelManager = $this->app['oauth2.model_manager.factory']->getModelManager('code');
        $model = $modelManager->createCode();
        $model->setCode('08fb55e26c84f8cb060b7803bc177af8')
            ->setClientId('http://democlient4.com/')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setUsername('demousername4')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateCode($model);

        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => '08fb55e26c84f8cb060b7803bc177af8',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient4.com/',
            'PHP_AUTH_PW' => 'demosecret4',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExceptionAuthCodeBadRedirectUri()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/wrong_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient2.com/',
            'PHP_AUTH_PW' => 'demosecret2',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorAuthCodeNoCode()
    {
        $request = new Request();
        $parameters = array(
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionWrongClientIdAuthCode()
    {
        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => 'f0c68d250bcc729eb780a235371a9a55',
            'redirect_uri' => 'http://democlient2.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionExpiredAuthCode()
    {
        $modelManager = $this->app['oauth2.model_manager.factory']->getModelManager('code');
        $model = $modelManager->createCode();
        $model->setCode('08fb55e26c84f8cb060b7803bc177af8')
            ->setClientId('http://democlient1.com/')
            ->setExpires(new \DateTime('-10 minutes'))
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateCode($model);

        $parameters = array(
            'grant_type' => 'authorization_code',
            'code' => '08fb55e26c84f8cb060b7803bc177af8',
            'redirect_uri' => 'http://democlient1.com/redirect_uri',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }
}
