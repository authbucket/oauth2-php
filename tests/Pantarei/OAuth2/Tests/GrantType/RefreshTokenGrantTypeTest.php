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

use Pantarei\OAuth2\GrantType\RefreshTokenGrantType;
use Pantarei\OAuth2\Tests\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test refresh token grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RefreshTokenGrantTypeTest extends WebTestCase
{
    public function testGrantType()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '288b5ea8e75d2b24368a79ed5ed9593b',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new RefreshTokenGrantType($request, $this->app);

        $grant_type->setRefreshToken('37ed55a16777958a3953088576869ca7');
        $this->assertEquals('37ed55a16777958a3953088576869ca7', $grant_type->getRefreshToken());

        $grant_type->setScope('demoscope2');
        $this->assertEquals('demoscope2', $grant_type->getScope());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testNoRefreshToken()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'refresh_token',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new RefreshTokenGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('refresh_token', $grant_type->getName());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testBadRefreshToken()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '37ed55a16777958a3953088576869ca7',
            'scope' => 'demoscope1 demoscope2 demoscope3',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient3.com/',
            'PHP_AUTH_PW' => 'demosecret3',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new RefreshTokenGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('refresh_token', $grant_type->getName());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testExpiredRefreshToken()
    {
        $refresh_token = new $this->app['oauth2.entity.refresh_tokens']();
        $refresh_token->setRefreshToken('5ddaa68ac1805e728563dd7915441408')
            ->setClientId('http://democlient1.com/')
            ->setExpires(time() - 3600)
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->app['oauth2.orm']->persist($refresh_token);
        $this->app['oauth2.orm']->flush();

        $request = new Request();
        $post = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => '5ddaa68ac1805e728563dd7915441408',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new RefreshTokenGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('refresh_token', $grant_type->getName());
    }
}
