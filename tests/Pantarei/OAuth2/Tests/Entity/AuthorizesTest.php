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

use Pantarei\OAuth2\Entity\Authorizes;
use Pantarei\OAuth2\Tests\WebTestCase;

/**
 * Test authorizes entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizesTest extends WebTestCase
{
    public function testAbstract()
    {
        $data = new Authorizes();
        $data->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->assertEquals('http://democlient1.com/', $data->getClientId());
        $this->assertEquals('demousername1', $data->getUsername());
        $this->assertEquals(array('demoscope1'), $data->getScope());
    }

    public function testFind()
    {
        $result = $this->app['oauth2.orm']->find('Pantarei\OAuth2\Entity\Authorizes', 1);
        $this->assertEquals('Pantarei\OAuth2\Entity\Authorizes', get_class($result));
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('http://democlient1.com/', $result->getClientId());
        $this->assertEquals('demousername1', $result->getUsername());
        $this->assertEquals(array('demoscope1'), $result->getScope());
    }
}
