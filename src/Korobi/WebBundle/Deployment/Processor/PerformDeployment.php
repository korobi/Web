<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Util\GitInfo;

/**
 * Runs the deployment shell script.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class PerformDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $info) {
        $this->logger->debug('About to execute ' . $info->getRootPath() . 'deploy_init.sh');

        // move to the root path, or you'll get screamed at because 'app/console' could not be found
        chdir($info->getRootPath());

        $execOutput = [];
        $statusCode = -1;
        $gitInfo = $this->gitInfo;
        $info->getRevision()->setOldCommit($gitInfo->getHash());

        if (exec('./deploy_init.sh', $execOutput, $statusCode) === false) {
            $this->akio->message()->red()->text('Deployment failed.')->send('deploy');
            $info->getRevision()->setDeploySuccessful(false);
            $this->logger->debug('Failed to run deploy script.', [], true);
            $info->addStatus(DeploymentStatus::DEPLOY_FAILED);
        } else {
            $this->logger->debug('Deploy output: ', $execOutput);
            $info->getRevision()->setDeploySuccessful(true);
        }
        $info->getRevision()->setDeployOutput(implode("\n", $execOutput));

        // get latest git info
        $gitInfo->updateData($info->getRootPath());
        $info->getRevision()->setNewCommit($gitInfo->getHash());
        $info->getRevision()->setBranch($gitInfo->getBranch());
        return parent::handle($info);
    }
}
