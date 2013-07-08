<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests\Exception;

use PantaRei\OAuth2\Exception\UnsupportedGrantTypeException;

/**
 * Test unsupported grant type exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UnsupportedGrantTypeExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PantaRei\OAuth2\Exception\UnsupportedGrantTypeException
     */
    public function testUnsupportedGrantTypeException()
    {
        throw new UnsupportedGrantTypeException();
    }
}
