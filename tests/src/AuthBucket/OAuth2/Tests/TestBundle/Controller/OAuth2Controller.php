<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\TestBundle\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class OAuth2Controller
{
    public function oauth2IndexAction(Request $request, Application $app)
    {
        return $app['twig']->render('oauth2/index.html.twig');
    }

    public function oauth2LoginAction(Request $request, Application $app)
    {
        $error = $app['security.last_error']($request);

        return $app['twig']->render('oauth2/login.html.twig', array(
            'error' => $error,
        ));
    }
}
