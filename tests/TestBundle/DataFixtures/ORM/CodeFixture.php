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

use AuthBucket\OAuth2\Tests\TestBundle\Entity\Code;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CodeFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $model = new Code();
        $model->setCode('f0c68d250bcc729eb780a235371a9a55')
            ->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setRedirectUri('http://democlient2.com/redirect_uri')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $manager->persist($model);

        $model = new Code();
        $model->setCode('1e5aa97ddaf4b0228dfb4223010d4417')
            ->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setRedirectUri('http://democlient1.com/redirect_uri')
            ->setExpires(new \DateTime('-10 minutes'))
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Code();
        $model->setCode('08fb55e26c84f8cb060b7803bc177af8')
            ->setClientId('http://democlient4.com/')
            ->setUsername('demousername4')
            ->setRedirectUri('http://democlient4.com/redirect_uri')
            ->setExpires(new \DateTime('+10 minutes'))
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $manager->flush();
    }
}
