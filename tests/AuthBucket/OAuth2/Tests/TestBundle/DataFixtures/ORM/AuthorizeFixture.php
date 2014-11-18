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

use AuthBucket\OAuth2\Tests\TestBundle\Entity\Authorize;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AuthorizeFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $model = new Authorize();
        $model->setClientId('51b2d34c3a661b5e111a694dfcb4b248')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('6b44c21ef7bc8ca7380bb5b8276b3f97')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
                'demoscope4',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('authorization_code_grant')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('implicit_grant')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('resource_owner_password_credentials_grant')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('client_credentials_grant')
            ->setUsername('')
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('http://democlient1.com/')
            ->setUsername('demousername1')
            ->setScope(array(
                'demoscope1',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('http://democlient2.com/')
            ->setUsername('demousername2')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('http://democlient3.com/')
            ->setUsername('demousername3')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $manager->persist($model);

        $model = new Authorize();
        $model->setClientId('http://democlient1.com/')
            ->setUsername('')
            ->setScope(array(
                'demoscope1',
                'demoscope2',
                'demoscope3',
            ));
        $manager->persist($model);

        $manager->flush();
    }
}
