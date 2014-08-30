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

use AuthBucket\OAuth2\Tests\TestBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $model = new User();
        $model->setUsername('demousername1')
            ->setPassword('demopassword1')
            ->setRoles(array(
                'ROLE_USER',
            ));
        $manager->persist($model);

        $model = new User();
        $model->setUsername('demousername2')
            ->setPassword('demopassword2')
            ->setRoles(array(
                'ROLE_USER',
            ));
        $manager->persist($model);

        $model = new User();
        $model->setUsername('demousername3')
            ->setPassword('demopassword3')
            ->setRoles(array(
                'ROLE_USER',
            ));
        $manager->persist($model);

        $manager->flush();
    }
}
