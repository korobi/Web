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

    private $rootPath;

    public function __construct(EngineInterface $templating, LoggerInterface $logger) {
        $this->templating = $templating;
        $this->logger = $logger;
    }

    private function verifyHookHmac($signature, $secret, $data) {
        return hash_equals(hash_hmac("sha1", $data, $secret), $signature);
    }

    public function setHmacKey($hmacKey) {
        $this->hmacKey = $hmacKey;
    }

    public function deployAction(Request $request) {
        $signature = $this->getHmacSignatureFromRequest($request);

        $verified = $this->verifyHookHmac($signature, $this->hmacKey, $request->getContent());
        $theData = ["verified" => $verified, "data" => $this->hmacKey, "attributes" => $this->getJsonRequestData($request)];
        $this->logger->info("Got deploy request.", $theData);

        if ($verified) {
            $out = [];
            $this->logger->info("About to execute " . $this->rootPath . DIRECTORY_SEPARATOR . "deploy_init.sh");
            $retVal = exec($this->rootPath . DIRECTORY_SEPARATOR . "deploy_init.sh", $out);
            if ($retVal === false) {
                $this->logger->error("Failed to run deploy script.");
            } else {
                $this->logger->info("Cmd output: ", $out);
            }

        }

        return new JsonResponse($theData);
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath) {
        $this->rootPath = $rootPath . "..";
    }


    private function getHmacSignatureFromRequest(Request $request) {
        $sig = $request->headers->get("X-Hub-Signature");
        if ($sig === null) {
            return null;
        }
        parse_str($sig, $output);
        $sig = (array_key_exists("sha1", $output)) ? $output['sha1'] : null;
        return $sig;
    }
}
