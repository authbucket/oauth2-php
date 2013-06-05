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
use Symfony\Component\Security\Core\User\User;

/**
 * Test the UsersRepository functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UsersRepositoryTest extends WebTestCase
{
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testBadUsername()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.users']);
        $user = $provider->loadUserByUsername('badusername1');
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'badusername1');
    }

    public function testGoodUsername()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.users']);
        $user = $provider->loadUserByUsername('demousername1');
        $this->assertEquals($user->getUsername(), 'demousername1');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testBadRefreshUser()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.users']);
        $user = new User('demousername1', 'demopassword1');
        $user = $provider->refreshUser($user);
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'demousername1');
    }

    public function testGoodRefreshUser()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.users']);
        $user = $provider->loadUserByUsername('demousername1');
        $this->assertEquals($user->getUsername(), 'demousername1');
        $user = $provider->refreshUser($user);
        $this->assertEquals($user->getUsername(), 'demousername1');
    }
}
