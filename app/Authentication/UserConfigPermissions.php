<?php


namespace Korobi\Authentication;


use Illuminate\Contracts\Config\Repository;

/**
 * Class UserConfigPermissions
 * @package Korobi\Authentication
 */
class UserConfigPermissions implements UserPermissionsInterface {

    /**
     * @var int[] Stores the list of admins.
     */
    private $users;

    /**
     * @param Repository $configRepository The configuration repository.
     */
    public function __construct(Repository $configRepository) {
        $this->users = $configRepository->get("admins.ids", []);
    }


    /**
     * @param int $id The user's unique identifier.
     * @return bool Whether the user is an admin or not.
     */
    public function isAdministrator($id) {
        return in_array($id, $this->users);
    }
}