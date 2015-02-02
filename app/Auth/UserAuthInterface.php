<?php

namespace Korobi\Auth;

interface UserAuthInterface {

    public function isAuthenticated();

    public function getUsername();

    public function getUniqueId();

    public function isAdmin();
}
