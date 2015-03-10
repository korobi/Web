<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Doctrine\ODM\MongoDB\DocumentManager;
use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentLogger;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Util\Akio;

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
     * @var DeploymentLogger
     */
    protected $logger;
    /**
     * @var Akio
     */
    protected $akio;

    /**
     * @var DocumentManager
     */
    protected $dm;

    public function __construct(DeploymentLogger $logger, Akio $akio, DocumentManager $dm) {
        $this->logger = $logger;
        $this->akio = $akio;
        $this->dm = $dm;
    }


    /**
     * @param DeploymentInfo $deploymentInfo
     * @return string Status of deployment
     */
    public function handle(DeploymentInfo $deploymentInfo) {
        if ($this->next !== null) {
            return $this->next->handle($deploymentInfo);
        }
        return DeploymentStatus::OK;
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
