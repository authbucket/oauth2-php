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

use AuthBucket\OAuth2\Tests\TestBundle\Entity\Client;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ClientFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request = Request::createFromGlobals();
        if (!$request->getUri()) {
            $request = Request::create('http://127.0.0.1:8000');
        }

        $model = new Client();
        $model->setClientId('51b2d34c3a661b5e111a694dfcb4b248')
            ->setClientSecret('237ed57f218b41d07db6757afec3a41c')
            ->setRedirectUri('http://oauthconnector.demo.drupal.authbucket.com/oauth/authorized2/1');
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('6b44c21ef7bc8ca7380bb5b8276b3f97')
            ->setClientSecret('54fe25c871b3ee81d037b6b22bed84b2')
            ->setRedirectUri('http://localhost');
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('authorization_code_grant')
            ->setClientSecret('uoce8AeP')
            ->setRedirectUri($request->getUriForPath('/demo/response_type/code'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('implicit_grant')
            ->setClientSecret('Ac1chee1')
            ->setRedirectUri($request->getUriForPath('/demo/response_type/token'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('resource_owner_password_credentials_grant')
            ->setClientSecret('Eevahph6')
            ->setRedirectUri($request->getUriForPath('/demo/grant_type/password'));
        $manager->persist($model);

        $model = new Client();
        $model->setClientId('client_credentials_grant')
            ->setClientSecret('yib6aiFe')
            ->setRedirectUri($request->getUriForPath('/demo/grant_type/client_credentials'));
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
