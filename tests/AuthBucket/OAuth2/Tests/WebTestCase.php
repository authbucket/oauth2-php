<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests;

use Silex\Application;
use Silex\WebTestCase as SilexWebTestCase;

/**
 * Extend Silex\WebTestCase for test case require database and web interface
 * setup.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class WebTestCase extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = new Application(array('env' => 'test'));

        require __DIR__.'/../../../../app/AppKernel.php';

        $app->boot();

        return $app;
    }
}
