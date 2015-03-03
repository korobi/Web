<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * Runs the deployment shell script.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class PerformDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        // TODO: Implement handle() method.
    }
}
