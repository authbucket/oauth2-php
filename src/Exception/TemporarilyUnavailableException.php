<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Exception;

/**
 * TemporarilyUnavailableException
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class TemporarilyUnavailableException extends \LogicException implements ExceptionInterface
{
    public function __construct($message = array(), $code = 503, Exception $previous = null)
    {
        $message['error'] = 'temporarily_unavailable';
        parent::__construct(serialize($message), $code, $previous);
    }
}
