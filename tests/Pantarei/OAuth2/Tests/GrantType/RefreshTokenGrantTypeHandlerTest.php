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

class RefreshTokenGrantTypeHandlerTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorRefreshTokenNoToken()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'scope' => 'demoscope1 demoscope2 demoscope3',
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
     * @expectedException \Pantarei\OAuth2\Exception\InvalidScopeException
     */
    public function testErrorRefreshTokenBadScope()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => "badscope1",
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testErrorRefreshTokenBadScopeFormat()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => "demoscope1\x22demoscope2\x5cdemoscope3",
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionRefreshTokenBadClientId()
    {
        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $client = $this->createClient();
        $crawler = $client->request('POST', '/token', $parameters, array(), $server);
        $this->assertNotNull(json_decode($client->getResponse()->getContent()));
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testExceptionRefreshTokenExpired()
    {
        // Add demo refresh token.
        $modelManager = $this->app['oauth2.model_manager.factory']->getModelManager('refresh_token');
        $model = $modelManager->createRefreshToken();
        $model->setRefreshToken('5ff43cbc27b54202c6fd8bb9c2a308ce')
            ->setClientId('http://democlient1.com/')
            ->setExpires(new \DateTime('-1 days'))
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $modelManager->updateRefreshToken($model);

        $parameters = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '5ff43cbc27b54202c6fd8bb9c2a308ce',
            'scope' => 'demoscope1',
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
