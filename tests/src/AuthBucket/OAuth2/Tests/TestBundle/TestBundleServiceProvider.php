<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle;

use AuthBucket\OAuth2\Tests\TestBundle\Controller\AuthorizeController;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\ClientController;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\DefaultController;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\DemoController;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\OAuth2Controller;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\ResourceController;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Test bundle provider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TestBundleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['testbundle.authorize_controller'] = $app->share(function () {
            return new AuthorizeController();
        });

        $app['testbundle.client_controller'] = $app->share(function () {
            return new ClientController();
        });

        $app['testbundle.default_controller'] = $app->share(function () {
            return new DefaultController();
        });

        $app['testbundle.demo_controller'] = $app->share(function () {
            return new DemoController();
        });

        $app['testbundle.oauth2_controller'] = $app->share(function () {
            return new OAuth2Controller();
        });

        $app['testbundle.resource_controller'] = $app->share(function () {
            return new ResourceController();
        });
    }

    public function boot(Application $app)
    {
    }
}
