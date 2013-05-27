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

use Pantarei\OAuth2\Entity\AccessTokens;
use Pantarei\OAuth2\WebTestCase;

/**
 * Test access tokens entity functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokensTest extends WebTestCase
{
    public function testAbstract()
    {
        $data = new AccessTokens();
        $data->setAccessToken('eeb5aa92bbb4b56373b9e0d00bc02d93')
            ->setTokenType('bearer')
            ->setClientId('http://democlient1.com/')
            ->setExpires(time() + 28800)
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $data->getAccessToken());
        $this->assertEquals('bearer', $data->getTokenType());
        $this->assertEquals('http://democlient1.com/', $data->getClientId());
        $this->assertTrue($data->getExpires() > time());
        $this->assertEquals('demousername1', $data->getUsername());
        $this->assertEquals(array('demoscope1'), $data->getScope());
    }

    public function testFind()
    {
        $result = $this->app['oauth2.orm']->find('Pantarei\OAuth2\Entity\AccessTokens', 1);
        $this->assertEquals('Pantarei\OAuth2\Entity\AccessTokens', get_class($result));
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('eeb5aa92bbb4b56373b9e0d00bc02d93', $result->getAccessToken());
        $this->assertEquals('bearer', $result->getTokenType());
        $this->assertEquals('http://democlient1.com/', $result->getClientId());
        $this->assertTrue($result->getExpires() > time());
        $this->assertEquals('demousername1', $result->getUsername());
        $this->assertEquals(array('demoscope1'), $result->getScope());
    }

    public function testExpired()
    {
        $data = new AccessTokens();
        $data->setAccessToken('5ddaa68ac1805e728563dd7915441408')
            ->setTokenType('bearer')
            ->setClientId('http://democlient4.com/')
            ->setExpires(time() - 3600)
            ->setUsername('demousername4')
            ->setScope(array(
                'demoscope1',
            ));
        $this->app['oauth2.orm']->persist($data);
        $this->app['oauth2.orm']->flush();

        $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\AccessTokens')->findOneBy(array(
            'access_token' => '5ddaa68ac1805e728563dd7915441408',
        ));
        $this->assertTrue($result !== null);
        $this->assertTrue($result->getExpires() < time());
    }
}
