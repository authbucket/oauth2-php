<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Util;

use Pantarei\OAuth2\OAuth2WebTestCase;
use Pantarei\OAuth2\Util\ParameterUtils;

/**
 * Testing parameter utility functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ParameterUtilsTest extends OAuth2WebTestCase
{
  public function testReturnAll()
  {
    $array = array(
      'client_id' => 'democlient1',
      'client_secret' => 'demosecret1',
    );
    $filtered_array = ParameterUtils::filter($array);
    $this->assertEquals($array, $filtered_array);
  }

  public function testReturnSubset()
  {
    $array = array(
      'client_id' => 'democlient1',
      'client_secret' => 'demosecret1',
    );
    $params = array('client_id');
    $filtered_array = ParameterUtils::filter($array, $params);
    $this->assertEquals(1, count($filtered_array));
    $this->assertEquals('democlient1', $filtered_array['client_id']);
  }
}
