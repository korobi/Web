<?php


namespace Korobi\WebBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener {

    /**
     * Handle kernel exceptions that come in and provide responses for exceptions we know about.
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) {
        // TODO: Implementation
        // $event->setResponse(...)
    }
}
