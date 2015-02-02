<?php

namespace Korobi\Auth;

interface UserPermissionsInterface {

    /**
     * @param int $id The user's unique identifier.
     */
    public function isAdmin($id);
}
