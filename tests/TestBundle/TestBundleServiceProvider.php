<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle;

use AuthBucket\OAuth2\Tests\TestBundle\Controller\DefaultController;
use AuthBucket\OAuth2\Tests\TestBundle\Controller\DemoController;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

/**
 * Test bundle provider.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TestBundleServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['authbucket_oauth2.tests.default_controller'] = function () {
            return new DefaultController();
        };

        $app['authbucket_oauth2.tests.demo_controller'] = function () {
            return new DemoController();
        };
    }

    public function boot(Application $app)
    {
    }
}
