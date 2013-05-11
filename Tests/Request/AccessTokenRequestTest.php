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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test authorization code grant type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessTokenRequestTest extends OAuth2WebTestCase
{
  public function createApplication()
  {
    $app = parent::createApplication();

    $app->get('/validaterequest', function(Request $request) {
      $response = new Response();
      $controller = new AccessTokenRequest();

      $response_type = $controller->validateRequest($request->query->all());
      return (is_object($response_type))
      ? $response->setStatusCode(200)
      : $response->setStatusCode(404);
    });

    return $app;
  }

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

  public function testValidateRequestGoodGrantType()
  {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/validaterequest', array(
      'grant_type' => 'client_credentials',
    ));
    $this->assertTrue($client->getResponse()->isSuccessful());
  }
}
