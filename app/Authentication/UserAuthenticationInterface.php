<?php


namespace Korobi\Authentication;


interface UserAuthenticationInterface {

    public function isAuthenticated();
    public function getUsername();
    public function getUniqueIdentifier();
    public function isAdmin();

} 