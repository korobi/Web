<?php

namespace Korobi\WebBundle\Deployment;

use Korobi\WebBundle\Deployment\Processor\DeploymentProcessorInterface;
use Korobi\WebBundle\Deployment\Processor\FinalizeDeployment;
use Korobi\WebBundle\Deployment\Processor\PerformDeployment;
use Korobi\WebBundle\Deployment\Processor\RequestVerification;
use Korobi\WebBundle\Deployment\Processor\RunTests;

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

    public function __construct(DeploymentInfo $info, $logger, $kernel, $akio, $docManager, $gitInfo) {
        $logger = new DeploymentLogger($kernel, $logger);

        $firstStep = new RequestVerification($logger, $akio, $docManager, $gitInfo);
        $secondStep = new PerformDeployment($logger, $akio, $docManager, $gitInfo);
        $thirdStep = new RunTests($logger, $akio, $docManager, $gitInfo);
        $fourthStep = new FinalizeDeployment($logger, $akio, $docManager, $gitInfo);
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
