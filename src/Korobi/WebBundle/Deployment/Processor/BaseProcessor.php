<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

/**
 * Does base processing.
 * @package Korobi\WebBundle\Deployment\Processor
 */
abstract class BaseProcessor implements DeploymentProcessorInterface {


    public function __construct() {

    }

    public function handle(Revision $document, Request $request) {
        // TODO: Implement handle() method.
    }
}
