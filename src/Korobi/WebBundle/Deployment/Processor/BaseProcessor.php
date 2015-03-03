<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * All processors extend this.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
abstract class BaseProcessor implements DeploymentProcessorInterface {

    /**
     * @var DeploymentProcessorInterface
     */
    private $next;

    /**
     * @param DeploymentInfo $deploymentInfo
     * @return string Status of deployment
     * @internal param Revision $document The revision document describing the deployment.
     * @internal param Request $request The HTTP request triggering it.
     */
    public function handle(DeploymentInfo $deploymentInfo) {
        if ($this->next !== null) {
            return $this->next->handle($deploymentInfo);
        }
        return DeploymentStatus::$OK;
    }

    /**
     * @return DeploymentProcessorInterface The next class in the COR structure.
     */
    public function getNext() {
        return $this->next;
    }

    /**
     * @param $next DeploymentProcessorInterface The next class in the COR structure.
     */
    public function setNext($next) {
        $this->next = $next;
    }


}
