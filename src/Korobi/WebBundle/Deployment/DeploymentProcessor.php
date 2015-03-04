<?php

namespace Korobi\WebBundle\Deployment;

use Korobi\WebBundle\Deployment\Processor\DeploymentProcessorInterface;
use Korobi\WebBundle\Deployment\Processor\FinalizeDeployment;
use Korobi\WebBundle\Deployment\Processor\PerformDeployment;
use Korobi\WebBundle\Deployment\Processor\RequestVerification;
use Korobi\WebBundle\Deployment\Processor\RunTests;
use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles deployments. Chain of responsibility.
 *
 * @package Korobi\WebBundle\Deployment
 */
class DeploymentProcessor {

    /**
     * @var DeploymentProcessorInterface
     */
    private $firstStep;

    /**
     * @var DeploymentInfo
     */
    private $info;

    public function __construct(DeploymentInfo $info, $logger, $kernel, $akio) {
        $logger = new DeploymentLogger($kernel, $logger);

        $firstStep = new RequestVerification($logger, $akio);
        $secondStep = new PerformDeployment($logger, $akio);
        $thirdStep = new RunTests($logger, $akio);
        $fourthStep = new FinalizeDeployment($logger, $akio);
        $firstStep->setNext($secondStep);
        $secondStep->setNext($thirdStep);
        $thirdStep->setNext($fourthStep);

        $this->firstStep = $firstStep;
        $this->info = $info;
    }

    /**
     * @return string Status of deployment
     * @see DeploymentStatus
     */
    public function performDeployment() {
        return $this->firstStep->handle($this->info);
    }
}
