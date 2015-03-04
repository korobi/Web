<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * Saves deployment results to the database.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class FinalizeDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        $this->dm->persist($deploymentInfo->getRevision());
        $this->dm->flush();
        $this->akio->sendMessage($this->akio->startMessage()->insertGreen()->insertText("https://dev.korobi.io/deploy/view/" . $deploymentInfo->getRevision()->getId()));

        return DeploymentStatus::$OK;
    }
}
