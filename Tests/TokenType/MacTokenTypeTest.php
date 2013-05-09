<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\TokenType;

use Pantarei\OAuth2\TokenType\MacTokenType;

/**
 * Test MAC token type functionality.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenTypeTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\Exception
   */
  public function testTokenType()
  {
    $grant_type = new MacTokenType();
    $this->assertEquals('mac', $grant_type->getTokenType());
  }
}
