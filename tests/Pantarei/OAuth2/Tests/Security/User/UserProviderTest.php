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
use Pantarei\Oauth2\Security\User\UserProvider;
use Symfony\Component\Security\Core\User\User;

/**
 * Test the UserProvider functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UserProviderTest extends WebTestCase
{
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testBadUsername()
    {
        $provider = new UserProvider($this->app);
        $user = $provider->loadUserByUsername('badusername1');
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'badusername1');
    }

    public function testGoodUsername()
    {
        $provider = new UserProvider($this->app);
        $user = $provider->loadUserByUsername('demousername1');
        $this->assertEquals($user->getUsername(), 'demousername1');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testBadRefreshUser()
    {
        $provider = new UserProvider($this->app);
        $user = new User('demousername1', 'demopassword1');
        $user = $provider->refreshUser($user);
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'demousername1');
    }

    public function testGoodRefreshUser()
    {
        $provider = new UserProvider($this->app);
        $user = $provider->loadUserByUsername('demousername1');
        $this->assertEquals($user->getUsername(), 'demousername1');
        $user = $provider->refreshUser($user);
        $this->assertEquals($user->getUsername(), 'demousername1');
    }
}
