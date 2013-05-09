<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Test\Exception;

use Pantarei\OAuth2\Exception\ServerErrorException;

/**
 * Test server error exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ServerErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\OAuth2\Exception\ServerErrorException
   */
  public function testServerErrorException()
  {
    throw new ServerErrorException();
  }
}
