<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;

/**
 * Runs the tests.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RunTests extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        chdir($deploymentInfo->getRootPath() . DIRECTORY_SEPARATOR . 'app');
        $execOutput = [];
        $testOutput = exec('phpunit', $execOutput);
        if (substr($testOutput, 0, 2) !== "OK") {
            $this->logger->debug("Tests failed!", [implode("\n", $execOutput)], true);

            $responseData['tests'] = ["status" => "fail", "output" => $execOutput];
        } else {
            $this->logger->debug("Tests passed.", [$testOutput]);
            $responseData['tests'] = ["status" => "pass", "output" => $execOutput];
        }
    }
}
