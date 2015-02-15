<?php

namespace Korobi\WebBundle\Util;

use Symfony\Component\HttpFoundation\Session\Session;

class ThemeService {

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function isLight() {
        return $this->session->has('theme-light');
    }
}
