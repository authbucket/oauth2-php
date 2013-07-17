<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\EventListener;

use Pantarei\OAuth2\Exception\ExceptionInterface;
use Pantarei\OAuth2\Util\JsonResponse;
use Pantarei\OAuth2\Util\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        // determine the actual cause for the exception
        while (null !== $previous = $exception->getPrevious()) {
            $exception = $previous;
        }

        if ($exception instanceof ExceptionInterface) {
            $response = $this->getResponse($exception);
        } else {
            return;
        }

        $event->setResponse($response);
    }

    private function getResponse(ExceptionInterface $exception)
    {
        $message = unserialize($exception->getMessage());
        $code = $exception->getCode();

        if (isset($message['redirect_uri'])) {
            $redirect_uri = $message['redirect_uri'];
            unset($message['redirect_uri']);
            $response = RedirectResponse::create($redirect_uri, $message);
        } else {
            $response = JsonResponse::create($message, $code);
        }

        return $response;
    }
}
