<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Security\User;

use Pantarei\OAuth2\WebTestCase;
use Pantarei\Oauth2\Security\User\ClientProvider;
use Symfony\Component\Security\Core\User\User;

/**
 * Test the ClientProvider functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientProviderTest extends WebTestCase
{
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testBadUsername()
    {
        $provider = new ClientProvider($this->app);
        $user = $provider->loadUserByUsername('http://badclient1.com/');
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'http://badclient1.com/');
    }

    public function testGoodUsername()
    {
        $provider = new ClientProvider($this->app);
        $user = $provider->loadUserByUsername('http://democlient1.com/');
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testBadRefreshUser()
    {
        $provider = new ClientProvider($this->app);
        $user = new User('http://democlient1.com/', 'demosecret1');
        $user = $provider->refreshUser($user);
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }

    public function testGoodRefreshUser()
    {
        $provider = new ClientProvider($this->app);
        $user = $provider->loadUserByUsername('http://democlient1.com/');
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
        $user = $provider->refreshUser($user);
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }
}
