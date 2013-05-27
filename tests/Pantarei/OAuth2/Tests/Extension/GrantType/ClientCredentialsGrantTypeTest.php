<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Extension\GrantType;

use Pantarei\OAuth2\Extension\GrantType\ClientCredentialsGrantType;
use Pantarei\OAuth2\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test client credential grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientCredentialsGrantTypeTest extends WebTestCase
{
    public function testGrantType()
    {
        $request = new Request();
        $post = array(
            'grant_type' => 'client_credentials',
            'scope' => 'demoscope1',
        );
        $server = array(
            'PHP_AUTH_USER' => 'http://democlient1.com/',
            'PHP_AUTH_PW' => 'demosecret1',
        );
        $request->initialize(array(), $post, array(), array(), array(), $server);
        $request->overrideGlobals();
        $grant_type = new ClientCredentialsGrantType($request, $this->app);

        $grant_type->setScope('demoscope2');
        $this->assertEquals('demoscope2', $grant_type->getScope());
    }
}
