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

use Pantarei\OAuth2\Entity\Clients;
use Pantarei\OAuth2\WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientsTest extends WebTestCase
{

    public function testAbstract()
    {
        $entity = new Clients();
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $entity->setClientId('http://democlient1.com/')
            ->setClientSecret($encoder->encodePassword('demosecret1', $entity->getSalt()))
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $this->assertEquals('http://democlient1.com/', $entity->getUsername());
        $this->assertEquals($encoder->encodePassword('demosecret1', $entity->getSalt()), $entity->getPassword());
        $this->assertEquals('http://democlient1.com/redirect_uri', $entity->getRedirectUri());
    }

    public function testFind()
    {
        $entity = $this->app['oauth2.orm']->find('Pantarei\OAuth2\Entity\Clients', 1);
        $encoder = $this->app['security.encoder_factory']->getEncoder($entity);
        $this->assertEquals('Pantarei\OAuth2\Entity\Clients', get_class($entity));
        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('http://democlient1.com/', $entity->getUsername());
        $this->assertEquals($encoder->encodePassword('demosecret1', $entity->getSalt()), $entity->getPassword());
        $this->assertEquals('http://democlient1.com/redirect_uri', $entity->getRedirectUri());
    }
}
