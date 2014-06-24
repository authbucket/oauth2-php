<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\DataFixtures\ORM;

use AuthBucket\OAuth2\Tests\Entity\Client;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ClientFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request = Request::createFromGlobals();
        if (!$request->getUri()) {
            $request = Request::create('http://localhost:8000');
        }

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

        $model = new Client();
        $model->setClientId('http://democlient1.com/')
            ->setClientSecret('demosecret1')
            ->setRedirectUri('http://democlient1.com/redirect_uri');
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('http://democlient2.com/')
            ->setClientSecret('demosecret2')
            ->setRedirectUri('http://democlient2.com/redirect_uri');
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('http://democlient3.com/')
            ->setClientSecret('demosecret3')
            ->setRedirectUri('http://democlient3.com/redirect_uri');
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('http://democlient4.com/')
            ->setClientSecret('demosecret4')
            ->setRedirectUri('');
        $manager->persist($model);

        $manager->flush();
    }
}
