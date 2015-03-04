<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentLogger;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Util\Akio;
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
     * @var DeploymentLogger
     */
    protected $logger;
    /**
     * @var Akio
     */
    protected $akio;

    public function __construct(DeploymentLogger $logger, Akio $akio) {
        $this->logger = $logger;
        $this->akio = $akio;
    }


    /**
     * @param DeploymentInfo $deploymentInfo
     * @return string Status of deployment
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
