<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Util\AkioMessageBuilder;

/**
 * Saves deployment results to the database.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class FinalizeDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $info) {
        $this->dm->persist($info->getRevision());
        $this->dm->flush();
        $this->messageQueue[] = $this->akio->message()->text('Full details at https://dev.korobi.io/deploy/view/' .
            $info->getRevision()->getId() . '/');

        if ($info->getRevision()->getOldCommit() !== $info->getRevision()->getNewCommit()) {
            /** @var AkioMessageBuilder $message */
            foreach ($this->messageQueue as $message) {
                $message->send("deploy");
            }
        }

        return DeploymentStatus::OK;
    }
}
