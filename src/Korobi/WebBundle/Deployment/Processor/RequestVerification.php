<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * Verifies the request is authentic (i.e. from GitHub, or a super admin).
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RequestVerification extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        $req = $deploymentInfo->getRequest();
    }
}
