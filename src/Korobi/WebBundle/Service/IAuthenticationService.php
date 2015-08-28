<?php

namespace Korobi\WebBundle\Service;

use Korobi\WebBundle\Document\Channel;
use Symfony\Component\HttpFoundation\Request;

interface IAuthenticationService {

    /**
     * @param Channel $dbChannel
     * @param Request $request
     * @return bool
     */
    public function hasAccessToChannel(Channel $dbChannel, Request $request);
}
