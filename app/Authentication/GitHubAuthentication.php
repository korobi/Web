<?php


namespace Korobi\Authentication;


use Illuminate\Session\Store;

class GitHubAuthentication implements UserAuthenticationInterface {

    /**
     * @var Store The session store instance.
     */
    private $session;


    public function __construct(Store $session) {
        $this->session = $session;
    }

    public function isAuthenticated() {
        return $this->session->get("auth.username") !== null;
    }

    public function getUsername() {
        return $this->session->get("auth.username");
    }

    public function getUniqueIdentifier() {
        return $this->session->get("auth.id");
    }

    public function isAdmin() {
        return $this->session->get("auth.admin");
    }
}