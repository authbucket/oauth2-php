<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Tests;

/**
 * Test if autoload able to discover all required classes.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AutoloadTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionClassesExist()
    {
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\AccessDeniedException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\InvalidClientException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\InvalidGrantException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\InvalidRequestException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\InvalidScopeException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\ServerErrorException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\TemporarilyUnavailableException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\UnauthorizedClientException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\UnsupportedGrantTypeException'));
        $this->assertTrue(class_exists('AuthBucket\OAuth2\Exception\UnsupportedResponseTypeException'));
    }
}
