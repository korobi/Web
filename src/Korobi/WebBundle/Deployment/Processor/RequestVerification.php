<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

/**
 * Verifies the request is legit (i.e. from a super admin or GitHub).
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RequestVerification extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(Revision $document, Request $request) {
        // TODO: Implement handle() method.
    }
}
