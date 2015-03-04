<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentProcessor;
use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Util\Akio;
use Korobi\WebBundle\Util\GitInfo;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeploymentController extends BaseController {

    /**
     * @var LoggerInterface Logger we can use.
     */
    private $logger;

    /**
     * @var string The root path (one up from kernel root).
     */
    private $rootPath;

    /**
     * @var string The HMAC secret used to ensure deploy requests from GitHub are valid.
     */
    private $hmacKey;

    /**
     * @var GitInfo
     */
    private $gitInfo;

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @param LoggerInterface $logger
     * @param GitInfo $gitInfo
     */
    public function __construct(LoggerInterface $logger, GitInfo $gitInfo, Akio $akio) {
        $this->logger = $logger;
        $this->gitInfo = $gitInfo;
        $this->akio = $akio;
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath) {
        $this->rootPath = $rootPath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $key
     */
    public function setHmacKey($key) {
        $this->hmacKey = $key;
    }

    public function deployAction(Request $request) {
        /** @var \Korobi\WebBundle\Document\User $user */
        $user = $this->getUser();

        $info = new DeploymentInfo($request, new Revision(), $user, $this->authChecker, $this->hmacKey, $this->rootPath);
        $processor = new DeploymentProcessor($info, $this->logger, $this->container->get('kernel'), $this->akio);
        $status = $processor->performDeployment();
        $this->akio->sendMessage($this->akio->startMessage()->insertGreen()->insertText("lol768: Status was " . $status));

        return new JsonResponse(["status" => $status]);
    }

    /**
     * Only log if debug is enabled.
     *
     * @param string $message The log message.
     * @param array $context The log context.
     * @param bool $error If this log entry is an error.
     */
    private function debug($message, array $context = array(), $error = false) {
        if ($this->container->get('kernel')->isDebug()) {
            if ($error) {
                $this->logger->error($message, $context);
            } else {
                $this->logger->info($message, $context);
            }
        }
    }

    /**
     * Get the signature from the request, if available.
     *
     * @param Request $request
     * @return array|null|string
     */
    private function getSignatureFromRequest(Request $request) {
        $signature = $request->headers->get('X-Hub-Signature');
        if ($signature === null) {
            return null;
        }

        parse_str($signature, $output);
        $signature = array_key_exists('sha1', $output) ? $output['sha1'] : null;

        return $signature;
    }

    /**
     * Verify the signature with our secret.
     *
     * @param $signature
     * @param $secret
     * @param $data
     * @return bool
     */
    private function verifySignature($signature, $secret, $data) {
        /** @noinspection PhpUndefinedFunctionInspection */ // hash_equals (PHP 5 >= 5.6.0) - PhpStorm complains about an undefined function
        return $signature !== null ? hash_equals(hash_hmac('sha1', $data, $secret), $signature) : false;
    }

    /**
     * @param Request $request
     * @param $verified
     * @return array
     */
    public function getInitialResponseData(Request $request, $verified) {
        $responseData = [
            'verified' => $verified,
            'hidden' => true,
            'data' => $this->hmacKey,
            'attributes' => $this->getJsonRequestData($request),
            'old_commit' => $this->gitInfo->getHash(),
            'branch' => $this->gitInfo->getBranch()
        ];
        return $responseData;
    }
}
