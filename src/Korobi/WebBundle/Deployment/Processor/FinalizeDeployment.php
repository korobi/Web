<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

/**
 * Saves deployment results to the database.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class FinalizeDeployment extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(Revision $document, Request $request) {
        // TODO: Implement handle() method.
    }
}
