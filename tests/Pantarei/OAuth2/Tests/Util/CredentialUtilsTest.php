<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Util;

use Pantarei\OAuth2\WebTestCase;
use Pantarei\OAuth2\Util\CredentialUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Testing parameter utility functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CredentialUtilsTest extends WebTestCase
{
    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testNoClientId()
    {
        $request = new Request();
        $post = array();
        $server = array();
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        // This won't happened!!
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testBothClientId()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        // This won't happened!!
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
    public function testBadPostClientId()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://badclient1.com/',
            'client_secret' => 'badsecret1',
        );
        $server = array();
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        // This won't happened!!
        $this->assertFalse($result);
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidClientException
     */
    public function testBadBasicClientId()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://badclient1.com/',
            'PHP_AUTH_PW' => 'badsecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        // This won't happened!!
        $this->assertFalse($result);
    }

    public function testGoodPostClientId()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
            'client_id' => 'http://democlient1.com/',
            'client_secret' => 'demosecret1',
        );
        $server = array();
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        $this->assertTrue($result);
    }

    public function testGoodBasicClientId()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'authorization_code',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $result = CredentialUtils::check($request, $this->app);
        $this->assertTrue($result);
    }
}
