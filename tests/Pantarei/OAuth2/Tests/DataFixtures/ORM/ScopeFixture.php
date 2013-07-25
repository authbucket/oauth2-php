<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pantarei\OAuth2\Tests\Entity\Scope;

class ScopeFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $model = new Scope();
        $model->setScope('demoscope1');
        $manager->persist($model);
        $model = new Scope();
        $model->setScope('demoscope2');
        $manager->persist($model);
        $model = new Scope();
        $model->setScope('demoscope3');
        $manager->persist($model);

        $manager->flush();
    }
}
