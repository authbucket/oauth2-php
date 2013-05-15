<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\ResponseType;

use Pantarei\OAuth2\ResponseType\CodeResponseType;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test code response type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class CodeResponseTypeTest extends OAuth2WebTestCase
{
  public function testResponseType()
  {
    $query = array(
      'response_type' => 'code',
      'client_id' => 'http://democlient1.com/',
      'redirect_uri' => 'http://democlient1.com/redirect_uri',
      'scope' => 'demoscope1',
      'state' => 'demostate1',
    );
    $response_type = new CodeResponseType($query, $query);
    $this->assertEquals('code', $response_type->getResponseType());

    $response_type->setClientId('5678');
    $this->assertEquals('5678', $response_type->getClientId());

    $response_type->setRedirectUri('http://abc.com/redirect');
    $this->assertEquals('http://abc.com/redirect', $response_type->getRedirectUri());

    $response_type->setScope('ddd eee fff');
    $this->assertEquals('ddd eee fff', $response_type->getScope());

    $response_type->setState('example state');
    $this->assertEquals('example state', $response_type->getState());
  }
}
