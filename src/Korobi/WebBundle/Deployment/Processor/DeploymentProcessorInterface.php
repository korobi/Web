<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;

interface DeploymentProcessorInterface {

    /**
     * @param DeploymentInfo $info
     * @return string Status of deployment
     */
    public function handle(DeploymentInfo $info);
}
