<?php

namespace Korobi\WebBundle\Listener;

use Korobi\WebBundle\Exception\CustomPageExceptionInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener {
    const TEMPLATE_NAME_PREFIX = "KorobiWebBundle:error/:";

    /**
     * @var TwigEngine
     */
    private $templatingEngine;

    /**
     * ExceptionListener constructor.
     * @param TwigEngine $templatingEngine
     */
    public function __construct(TwigEngine $templatingEngine) {
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * Handle kernel exceptions that come in and provide responses for exceptions we know about.
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) {
        if ($event->getException() instanceof CustomPageExceptionInterface) {
            /** @var CustomPageExceptionInterface|\Exception $exception */
            $exception = $event->getException();
            $response = $this->getResponseForException($exception);
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    /**
     * @param $exception
     * @return array
     */
    private function getViewParametersForException($exception) {
        return ["exception" => $exception];
    }

    /**
     * @param CustomPageExceptionInterface|\Exception $exception
     * @return Response
     */
    private function getResponseForException($exception) {
        $viewName = self::TEMPLATE_NAME_PREFIX . $exception->getViewName() . ".html.twig";
        $response = $this->templatingEngine->renderResponse($viewName, $this->getViewParametersForException($exception));
        $response->setStatusCode($exception->getCode());
        return $response;
    }
}
