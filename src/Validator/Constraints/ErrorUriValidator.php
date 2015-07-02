<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Validator\Constraints;

use Symfony\Component\Validator\Constraints\UrlValidator;

/**
 * Validates whether the value is a valid error_uri per RFC 6749
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * @see http://tools.ietf.org/html/rfc6749#appendix-A.9
 */
class ErrorUriValidator extends UrlValidator
{
}
