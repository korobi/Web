<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;
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
     * @param LoggerInterface $logger
     */
    public function __construct(Akio $akio, LoggerInterface $logger) {
        $this->akio = $akio;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportCspAction(Request $request) {
        $payload = json_decode($request->getContent(), true);
        if ($payload === null) {
            $payload = [];
        }
        $uri = $payload['csp-report']['document-uri'];
        $resource = $payload['csp-report']['blocked-uri'];
        $this->logger->warning('CSP Warning', $payload);
        $ip = hash_hmac('sha1', $request->getClientIp(), 'bc604aedc9027a1f1880');
        $this->akio->message()
            ->red()
            ->text('[!! CSP !!]')
            ->aquaLight()
            ->text(" Request to $resource on page $uri blocked via $ip.")
            ->send('csp', 'private');
        return new JsonResponse('Thanks, browser.');
    }

    /**
     * @param Request $req
     * @return Response
     */
    public function showRedirectAction(Request $req) {
        $response = new Response($this->renderView('KorobiWebBundle:error:unexpected-redirect.html.twig', [
            'url' => $req->get('redirUrl'),
        ]), 403);
        return $response;
    }

    public function safelyReturnScriptSource(Request $req) {
        $resp = $this->render("KorobiWebBundle:partial:analytics.js.twig");
        $resp->headers->set("Content-Type", "application/javascript");
        return $resp;
    }
}
