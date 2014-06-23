<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Demo\DataFixtures\ORM;

use AuthBucket\OAuth2\Tests\Entity\Client;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ClientFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request = Request::createFromGlobals();

        $model = new Client();
        $model->setClientId('acg')
            ->setClientSecret('uoce8AeP')
            ->setRedirectUri($request->getUriForPath('/response_type/code'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('ig')
            ->setClientSecret('Ac1chee1')
            ->setRedirectUri($request->getUriForPath('/response_type/token'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('ropcg')
            ->setClientSecret('Eevahph6')
            ->setRedirectUri($request->getUriForPath('/grant_type/password'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('ccg')
            ->setClientSecret('yib6aiFe')
            ->setRedirectUri($request->getUriForPath('/grant_type/client_credentials'));
        $manager->persist($model);

        $manager->flush();
    }
}
