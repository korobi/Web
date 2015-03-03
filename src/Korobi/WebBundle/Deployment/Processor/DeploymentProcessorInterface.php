<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;

interface DeploymentProcessorInterface {

    /**
     * @param DeploymentInfo $deploymentInfo
     * @return string Status of deployment
     */
    public function handle(DeploymentInfo $deploymentInfo);
}
