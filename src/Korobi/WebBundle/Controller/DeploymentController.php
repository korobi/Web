<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Repository\ChannelRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class DeploymentController extends BaseController {

    /**
     * @var EngineInterface Templating system.
     */
    private $templating;

    /**
     * @var LoggerInterface Logger we can use.
     */
    private $logger;

    /**
     * @var string The HMAC secret used to ensure deploy requests from GitHub are valid.
     */
    private $hmacKey;

    public function __construct(EngineInterface $templating, LoggerInterface $logger, $hmacKey) {
        $this->templating = $templating;
        $this->logger = $logger;
        $this->hmacKey = $hmacKey;
    }

    private function verifyHookHmac($signature, $secret, $data) {
        // TODO: Implement
    }

    public function deployAction(Request $request) {
        $this->logger->info("Got deploy request.");

        return new JsonResponse(["data" => $this->hmacKey, "attributes" => $this->getJsonRequestData($request)]);
    }
}
