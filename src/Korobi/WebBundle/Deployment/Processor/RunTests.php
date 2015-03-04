<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Deployment\TestOutputParser;

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
        $parsed = (new TestOutputParser())->parseLine($testOutput);
        if (substr($testOutput, 0, 2) !== "OK") {
            $this->logger->debug("Tests failed!", [implode("\n", $execOutput)], true);
            $this->akio->sendMessage($this->akio->startMessage()->insertText(json_encode($parsed)));
            return DeploymentStatus::$TESTS_FAILED;
        } else {
            $this->logger->debug("Tests passed.", [$testOutput]);
            $this->akio->sendMessage($this->akio->startMessage()->insertText(json_encode($parsed)));
            $responseData['tests'] = ["status" => "pass", "output" => $execOutput];
        }
        return parent::handle($deploymentInfo);
    }
}
