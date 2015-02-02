<?php

namespace Korobi\Auth;

use Illuminate\Contracts\Config\Repository;

/**
 * Class UserConfigPermissions
 * @package Korobi\Auth
 */
class UserConfigPermissions implements UserPermissionsInterface {

    /**
     * @var int[] Stores the list of admins.
     */
    private $admins;

    /**
     * @param Repository $configRepository The configuration repository.
     */
    public function __construct(Repository $configRepository) {
        $this->admins = $configRepository->get('permissions.admins.users', []);
    }

    /**
     * @param int $id The user's unique identifier.
     * @return bool Whether the user is an admin or not.
     */
    public function isAdmin($id) {
        return in_array($id, $this->admins);
    }
}
