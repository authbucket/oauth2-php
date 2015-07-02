<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle\DataFixtures\ORM;

use AuthBucket\OAuth2\Tests\TestBundle\Entity\RefreshToken;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RefreshTokenFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $model = new RefreshToken();
        $model->setRefreshToken('5ff43cbc27b54202c6fd8bb9c2a308ce')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setExpires(new \DateTime('-1 days'))
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new RefreshToken();
        $model->setRefreshToken('302a7e7af27a25a6c052302d0dcac2c0')
            ->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setExpires(new \DateTime('+1 days'))
            ->setScope(array(
                'unsupportedscope',
                'demoscope4',
            ));
        $manager->persist($model);

        $model = new RefreshToken();
        $model->setRefreshToken('288b5ea8e75d2b24368a79ed5ed9593b')
            ->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setExpires(new \DateTime('+1 days'))
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $manager->persist($model);

        $manager->flush();
    }
}
