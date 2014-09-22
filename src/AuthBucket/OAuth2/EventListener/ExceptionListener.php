<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\EventListener;

use AuthBucket\OAuth2\Exception\ExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * ExceptionListener.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        do {
            if ($exception instanceof ExceptionInterface) {
                return $this->handleException($event, $exception);
            }
        } while (null !== $exception = $exception->getPrevious());
    }

    private function handleException(
        GetResponseForExceptionEvent $event,
        ExceptionInterface $exception
    ) {
        $message = unserialize($exception->getMessage());

        if (isset($message['redirect_uri'])) {
            $redirectUri = $message['redirect_uri'];
            unset($message['redirect_uri']);
            $redirectUri = Request::create($redirectUri, 'GET', $message)->getUri();

            $response = RedirectResponse::create($redirectUri);
        } else {
            $code = $exception->getCode();

            $response = JsonResponse::create($message, $code, array(
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ));
        }

        $event->setResponse($response);
    }
}
