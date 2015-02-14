<?php


namespace Korobi\WebBundle\Util;


/**
 * POPO for NavigationItems
 * @package Korobi\Util
 */
class NavigationItem {

    private $title;
    private $route;
    private $requiresAuth;
    private $requiresAdmin;

    public function __construct($requiresAdmin, $requiresAuth, $title, $route) {
        $this->requiresAdmin = $requiresAdmin;
        $this->requiresAuth = $requiresAuth;
        $this->title = $title;
        $this->route = $route;
    }

    /**
     * @return boolean Whether or not the visibility of this item requires authentication.
     */
    public function getRequiresAdmin() {
        return $this->requiresAdmin;
    }

    /**
     * @return boolean
     */
    public function getRequiresAuth() {
        return $this->requiresAuth;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getRoute() {
        return $this->route;
    }
}
