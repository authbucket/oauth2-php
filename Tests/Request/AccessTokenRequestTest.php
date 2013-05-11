<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Request;

use Pantarei\OAuth2\Request\AccessTokenRequest;
use Pantarei\OAuth2\Tests\OAuth2WebTestCase;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequestTest extends OAuth2WebTestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestNoGrantType()
  {
    $request = new AccessTokenRequest();

    $query = array();
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }

  /**
   * @expectedException \Pantarei\OAuth2\Exception\InvalidRequestException
   */
  public function testValidateRequestBadGrantType()
  {
    $request = new AccessTokenRequest();

    $query = array(
      'grant_type' => 'foo',
    );
    $filtered_query = $request->validateRequest($query);
    // This won't happened!!
    $this->assertTrue(is_array($filtered_query));
  }
}
