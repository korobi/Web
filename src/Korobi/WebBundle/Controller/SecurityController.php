<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Util\Akio;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController {

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @var LoggerInterface Logger we can use.
     */
    private $logger;

    /**
     * @param Akio $akio
     */
    public function __construct(Akio $akio, LoggerInterface $logger) {
        $this->akio = $akio;
        $this->logger = $logger;
    }

    /**
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportCspAction(Request $req) {
        $payload = json_decode($req->getContent(), true);
        $uri = $payload['csp-report']['document-uri'];
        $resource = $payload['csp-report']['blocked-uri'];
        $this->logger->warning('CSP Warning', $payload);
        $ip = hash_hmac("sha1", $req->getClientIp(), "bc604aedc9027a1f1880");
        $message = $this->akio->startMessage()->insertRed()->insertText("[!! CSP !!]")->insertAquaLight()->insertText(" Request to $resource on page $uri blocked via $ip.");
        $this->akio->sendMessage($message);
        return new JsonResponse("Thanks, browser.");
    }

    public function showRedirectAction(Request $req) {
        $response = new Response($this->renderView("KorobiWebBundle::error-redirect.html.twig", ["url" => $req->get("redirUrl")]), 403);
        return $response;
    }
}
