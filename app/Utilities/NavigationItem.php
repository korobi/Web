<?php


namespace Korobi\Utilities;

/**
 * POPO for NavigationItems
 * @package Korobi\Utilities
 */
class NavigationItem {

    private $title;
    private $url;
    private $requiresAuth;
    private $requiresAdmin;

    public function __construct($requiresAdmin, $requiresAuth, $title, $url) {
        $this->requiresAdmin = $requiresAdmin;
        $this->requiresAuth = $requiresAuth;
        $this->title = $title;
        $this->url = $url;
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
    public function getUrl() {
        return $this->url;
    }




} 