<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Extension\GrantType;

use Pantarei\OAuth2\Extension\GrantType\PasswordGrantType;
use Pantarei\OAuth2\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test password grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class PasswordGrantTypeTest extends WebTestCase
{
    public function testGrantType()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new PasswordGrantType($request, $this->app);

        $grant_type->setUsername('demouser2');
        $this->assertEquals('demouser2', $grant_type->getUsername());

        $grant_type->setPassword('demopassword1');
        $this->assertEquals('demopassword1', $grant_type->getPassword());

        $grant_type->setScope('demoscope2');
        $this->assertEquals('demoscope2', $grant_type->getScope());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testNoUsername()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'password',
            'password' => 'demopassword1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new PasswordGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('password', $grant_type->getName());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testBadUsername()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'password',
            'username' => 'badusername1',
            'password' => 'demopassword1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new PasswordGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('password', $grant_type->getName());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
     */
    public function testNoPassword()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new PasswordGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('password', $grant_type->getName());
    }

    /**
     * @expectedException \Pantarei\OAuth2\Exception\InvalidGrantException
     */
    public function testBadPassword()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'password',
            'username' => 'demousername1',
            'password' => 'badpassword1',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new PasswordGrantType($request, $this->app);
        // This won't happened!!
        $this->assertEquals('password', $grant_type->getName());
    }
}
