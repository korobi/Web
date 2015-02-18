<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

/**
 * Runs the tests.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RunTests extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(Revision $document, Request $request) {
        // TODO: Implement handle() method.
    }
}
