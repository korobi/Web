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
        if ($parsed['failures'] > 0) {
            $this->logger->debug("Tests failed!", [implode("\n", $execOutput)], true);
            $this->akio->sendMessage($this->akio->startMessage()->insertText("Tests: " . $parsed['status']));
            $deploymentInfo->addStatus(DeploymentStatus::$TESTS_FAILED);
        } else {
            $this->logger->debug("Tests passed.", [$testOutput]);
            $this->akio->sendMessage($this->akio->startMessage()->insertText("Tests: " . $parsed['status']));
        }
        $deploymentInfo->getRevision()->setTestsOutput(implode("\n", $execOutput));
        $deploymentInfo->getRevision()->setTestsPassed($parsed['failures'] === 0);
        $deploymentInfo->getRevision()->setTestsInfo($parsed);
        return parent::handle($deploymentInfo);
    }
}
