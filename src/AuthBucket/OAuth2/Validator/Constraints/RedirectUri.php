<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Validator\Constraints;

use Symfony\Component\Validator\Constraint\Url;

/**
 * @Annotation
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RedirectUri extends Url
{
    public $message = 'This is not a valid redirect_uri.';
}
