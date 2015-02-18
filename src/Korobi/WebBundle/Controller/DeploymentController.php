<?php

namespace Korobi\WebBundle\Controller;

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
        $verified = $this->verifySignature($this->getSignatureFromRequest($request), $this->hmacKey, $request->getContent());
        $responseData = $this->getInitialResponseData($request, $verified);
        $this->debug('Got deploy request.', $responseData);

        $isSuperAdmin = $this->authChecker->isGranted('ROLE_SUPER_ADMIN');
        if ($verified || $isSuperAdmin) {
            $this->debug("About to execute " . $this->rootPath . 'deploy_init.sh');

            // move to the root path, or you'll get screamed at because 'app/console' could not be found
            chdir($this->rootPath);

            $execOutput = [];
            $statusCode = -1;
            if (exec('./deploy_init.sh', $execOutput, $statusCode) === false) {
                $this->akio->sendMessage($this->akio->startMessage()->insertRed()->insertText("lol768: Deploy failed."));
                $this->debug('Failed to run deploy script.', array(), true);
            } else {
                $this->debug('Deploy output: ', $execOutput);

            }

            $responseData['exec_output'] = $execOutput;
            $responseData['status_code'] = $statusCode;

            // get latest git info
            $this->gitInfo->updateData();
            $responseData['new_commit'] = $this->gitInfo->getHash();

            // we'll do tests here instead of in the bash script to make output processing easier
            chdir($this->rootPath . DIRECTORY_SEPARATOR . 'app');
            $execOutput = [];
            $testOutput = exec('phpunit', $execOutput);
            if (substr($testOutput, 0, 2) !== "OK") {
                $this->debug("Tests failed!", [implode("\n", $execOutput)], true);
                $responseData['tests'] = ["status" => "fail", "output" => $execOutput];
            } else {
                $this->debug("Tests passed.", [$testOutput]);
                $responseData['tests'] = ["status" => "pass", "output" => $execOutput];
            }
            
            
            if (true) {
                // code has changed, insert a new revision
                
                if ($responseData['tests']['status'] === 'pass') {
                    $this->akio->sendMessage($this->akio->startMessage()->insertGreen()->insertText("All tests passed! Output: " . $testOutput));
                } else {
                    $this->akio->sendMessage($this->akio->startMessage()->insertRed()->insertText("lol768: At least one test failed. Output: " . $testOutput));
                }
            }

            // only provide output if super admin
            if (!$isSuperAdmin) {
                return new JsonResponse(["verified" => $verified, "hidden" => true]);
            }
        }

        return new JsonResponse($responseData);
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
