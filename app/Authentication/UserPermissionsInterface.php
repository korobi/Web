<?php


namespace Korobi\Authentication;


interface UserPermissionsInterface {

    /**
     * @param int $id The user's unique identifier.
     */
    public function isAdministrator($id);

} 