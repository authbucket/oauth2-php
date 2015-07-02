<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ResourceController
{
    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('resource/index.html.twig');
    }
}
