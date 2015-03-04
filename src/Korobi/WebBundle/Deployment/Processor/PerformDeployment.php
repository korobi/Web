<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Util\GitInfo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Runs the deployment shell script.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class PerformDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        $this->logger->debug("About to execute " . $deploymentInfo->getRootPath() . 'deploy_init.sh');

        // move to the root path, or you'll get screamed at because 'app/console' could not be found
        chdir($deploymentInfo->getRootPath());

        $execOutput = [];
        $statusCode = -1;
        $gitInfo = new GitInfo();
        $deploymentInfo->getRevision()->setOldCommit($gitInfo->getHash());

        if (exec('./deploy_init.sh', $execOutput, $statusCode) === false) {
            $this->akio->sendMessage($this->akio->startMessage()->insertRed()->insertText("lol768: Deploy failed."));
            $deploymentInfo->getRevision()->setDeploySuccessful(false);
            $this->logger->debug('Failed to run deploy script.', array(), true);
            return DeploymentStatus::$DEPLOY_FAILED;
        } else {
            $this->logger->debug('Deploy output: ', $execOutput);
        }
        $deploymentInfo->getRevision()->setDeployOutput(implode("\n", $execOutput));

        // get latest git info
        $gitInfo->updateData();
        $deploymentInfo->getRevision()->setNewCommit($gitInfo->getHash());
        $deploymentInfo->getRevision()->setBranch($gitInfo->getBranch());
        return parent::handle($deploymentInfo);
    }
}
