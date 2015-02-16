<?php


namespace Korobi\WebBundle\Deployment\Processor;


use Korobi\WebBundle\Document\Revision;
use Symfony\Component\BrowserKit\Request;

interface DeploymentProcessorInterface {

    public function handle(Revision $document, Request $request);

}
