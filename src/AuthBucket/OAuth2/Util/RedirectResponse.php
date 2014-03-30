<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Util;

use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * OAuth2 specific RedirectResponse represents an HTTP response doing a redirect.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class RedirectResponse extends BaseRedirectResponse
{
    public static function create($url = '', $data = array(), $status = 302, $headers = array())
    {
        $url = Request::create($url, 'GET', $data)->getUri();
        return BaseRedirectResponse::create($url, $status, $headers);
    }
}
