<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests\Exception;

use AuthBucket\OAuth2\Exception\InvalidClientException;

/**
 * Test invalid client exception.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class InvalidClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AuthBucket\OAuth2\Exception\InvalidClientException
     */
    public function testInvalidClientException()
    {
        throw new InvalidClientException();
    }
}
