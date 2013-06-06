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

use Pantarei\OAuth2\Entity\Users;
use Pantarei\OAuth2\Tests\WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UsersTest extends WebTestCase
{
    public function testAbstract()
    {
        $entity = new Users();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setUsername('demousername1')
            ->setPassword($encoder->encodePassword('demopassword1', $entity->getSalt()));
        $this->assertEquals('demousername1', $entity->getUsername());
        $this->assertEquals($encoder->encodePassword('demopassword1', $entity->getSalt()), $entity->getPassword());
    }

    public function testFind()
    {
        $entity = $this->app['oauth2.orm']->find('Pantarei\OAuth2\Entity\Users', 1);
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $this->assertEquals('Pantarei\OAuth2\Entity\Users', get_class($entity));
        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('demousername1', $entity->getUsername());
        $this->assertEquals($encoder->encodePassword('demopassword1', $entity->getSalt()), $entity->getPassword());
    }
}
