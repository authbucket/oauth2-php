<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Entity;

use Pantarei\OAuth2\WebTestCase;
use Symfony\Component\Security\Core\User\User;

/**
 * Test the ClientsRepository functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientsRepositoryTest extends WebTestCase
{
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testBadUsername()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.clients']);
        $user = $provider->loadUserByUsername('http://badclient1.com/');
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'http://badclient1.com/');
    }

    public function testGoodUsername()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.clients']);
        $user = $provider->loadUserByUsername('http://democlient1.com/');
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testBadRefreshUser()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.clients']);
        $user = new User('http://democlient1.com/', 'demosecret1');
        $user = $provider->refreshUser($user);
        // This won't happened!!
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }

    public function testGoodRefreshUser()
    {
        $provider = $this->app['oauth2.orm']->getRepository($this->app['oauth2.entity.clients']);
        $user = $provider->loadUserByUsername('http://democlient1.com/');
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
        $user = $provider->refreshUser($user);
        $this->assertEquals($user->getUsername(), 'http://democlient1.com/');
    }
}
