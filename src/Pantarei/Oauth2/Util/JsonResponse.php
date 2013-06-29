<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Util;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

/**
 * OAuth2 specific Response represents an HTTP response in JSON format.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class JsonResponse extends BaseJsonResponse
{
    public static function create($data = null, $status = 200, $headers = array())
    {
        $headers = array_merge($headers, array(
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ));
        return BaseJsonResponse::create($data, $status, $headers);
    }
}
