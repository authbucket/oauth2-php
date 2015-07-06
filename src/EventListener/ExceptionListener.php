<?php

/*
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\EventListener;

use AuthBucket\OAuth2\Exception\ExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ExceptionListener.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ExceptionListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
        if (null !== $this->logger) {
            $message = sprintf(
                '%s: %s (code %s) at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getFile(),
                $exception->getLine()
            );

            if ($exception->getCode() < 500) {
                $this->logger->error($message, array('exception' => $exception));
            } else {
                $this->logger->critical($message, array('exception' => $exception));
            }
        }

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

    public static function getSubscribedEvents()
    {
        return array(
            /*
             * Priority -2 is used to come after those from SecurityServiceProvider (0)
             * but before the error handlers added with Silex\EventListener\LogListener (-4)
             * and Silex\Application::error (defaults to -8)
             */
            KernelEvents::EXCEPTION => array('onKernelException', -2),
        );
    }
}
