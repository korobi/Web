<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Util\Akio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController {

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @param Akio $akio
     */
    public function __construct(Akio $akio) {
        $this->akio = $akio;
    }

    /**
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reportCspAction(Request $req) {
        $payload = json_decode($req->getContent(), true);
        $uri = $payload['csp-report']['document-uri'];
        $resource = $payload['csp-report']['blocked-uri'];
        $message = $this->akio->startMessage()->insertRed()->insertText("[!! CSP !!]")->insertAquaLight()->insertText(" Request to $resource on page $uri blocked.");
        $this->akio->sendMessage($message);
        return new JsonResponse("Thanks, browser.");
    }

    public function showRedirectAction(Request $req) {
        $response = new Response($this->renderView("KorobiWebBundle::error-redirect.html.twig", ["url" => $req->get("redirUrl")]), 403);
        return $response;
    }
}