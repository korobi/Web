<?php

namespace Korobi\WebBundle\Deployment;

use Korobi\WebBundle\Document\Revision;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles deployments. Chain of responsibility.
 *
 * @package Korobi\WebBundle\Deployment
 */
class DeploymentProcessor {

    public function __construct(Request $request, Revision $revision) {
    }
}
