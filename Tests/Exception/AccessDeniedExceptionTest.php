<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Test\Exception;

use Pantarei\Oauth2\Exception\AccessDeniedException;

/**
 * Test access denied exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AccessDeniedExceptionTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException \Pantarei\Oauth2\Exception\AccessDeniedException
   */
  public function testAccessDeniedException()
  {
    throw new AccessDeniedException();
  }
}
