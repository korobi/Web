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

    /**
     * Does the actual test work.
     *
     * @param DeploymentInfo $info
     * @return string
     */
    public function handle(DeploymentInfo $info) {
        chdir($info->getRootPath() . DIRECTORY_SEPARATOR . 'app');
        $execOutput = [];
        $testOutput = exec('phpunit', $execOutput);
        $parsed = (new TestOutputParser())->parseLine($testOutput);

        $message = $this->akio->message()->green()->text($parsed['passed'] . ' tests passed.');

        if ($parsed['incomplete'] > 0) {
            $message = $message->yellow()->text(' ' . $parsed['incomplete'] . ' skipped/incomplete tests.');
        }

        if ($parsed['failures'] > 0) {
            $this->logger->debug('Tests failed!', [implode("\n", $execOutput)], true);
            $message = $message->red()->text(' ' . $parsed['failures'] . ' failed.');
            $info->addStatus(DeploymentStatus::TESTS_FAILED);
        }

        $message->send('deploy');

        $info->getRevision()->setTestsOutput(implode("\n", $execOutput));
        $info->getRevision()->setTestsPassed($parsed['failures'] === 0);
        $info->getRevision()->setTestsInfo($parsed);

        return parent::handle($info);
    }
}
