<?php

namespace Korobi\WebBundle\Service;

use Korobi\WebBundle\Document\Channel;
use Symfony\Component\HttpFoundation\Request;

interface IAuthenticationService {

    const ALLOW = 0;
    const INVALID_KEY = 1;
    const REJECT = 2;

    /**
     * @param Channel $dbChannel
     * @param Request $request
     * @return int
     */
    public function hasAccessToChannel(Channel $dbChannel, Request $request);
}
