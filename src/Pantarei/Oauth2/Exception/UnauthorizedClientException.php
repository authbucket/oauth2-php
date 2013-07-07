<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Exception;

/**
 * UnauthorizedClientException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class UnauthorizedClientException extends \LogicException implements ExceptionInterface
{
    public function __construct($message = array(), $code = 401, Exception $previous = null)
    {
        $message['error'] = 'unauthorized_client';
        parent::__construct(serialize($message), $code, $previous);
    }
}
