<?php


namespace Korobi\WebBundle\Deployment\Processor;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

/**
 * Runs the deployment shell script.
 * @package Korobi\WebBundle\Deployment\Processor
 */
class PerformDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(Revision $document, Request $request) {
        // TODO: Implement handle() method.
    }
}
