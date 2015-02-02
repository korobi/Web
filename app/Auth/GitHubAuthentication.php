<?php

namespace Korobi\Auth;

use Illuminate\Session\Store;

class GitHubAuth implements UserAuthInterface {

    /**
     * @var Store The session store instance.
     */
    private $session;

    public function __construct(Store $session) {
        $this->session = $session;
    }

    public function isAuthenticated() {
        return $this->session->get('auth.username') !== null;
    }

    public function getUsername() {
        return $this->session->get('auth.username');
    }

    public function getUniqueId() {
        return $this->session->get('auth.id');
    }

    public function isAdmin() {
        return $this->session->get('auth.admin');
    }
}
