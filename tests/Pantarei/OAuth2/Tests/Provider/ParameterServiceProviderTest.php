<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Provider;

use Pantarei\OAuth2\OAuth2WebTestCase;

/**
 * Testing parameter utility functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ParameterServiceProviderTest extends OAuth2WebTestCase
{
  public function testReturnAll()
  {
    $array = array(
      'client_id' => 'democlient1',
      'client_secret' => 'demosecret1',
    );
    $filtered_array = $this->app['oauth2.param.filter']($array);
    $this->assertEquals($array, $filtered_array);
  }

  public function testReturnSubset()
  {
    $array = array(
      'client_id' => 'democlient1',
      'client_secret' => 'demosecret1',
    );
    $params = array('client_id');
    $filtered_array = $this->app['oauth2.param.filter']($array, $params);

    $this->assertEquals(1, count($filtered_array));
    $this->assertEquals('democlient1', $filtered_array['client_id']);
  }
}
