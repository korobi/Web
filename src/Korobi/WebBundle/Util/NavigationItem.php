<?php

namespace Korobi\WebBundle\Util;

/**
 * POPO for NavigationItems
 * @package Korobi\Util
 */
class NavigationItem {

    private $title;
    private $route;
    private $routes;
    private $requiresAuth;
    private $requiresAdmin;
    private $externalUrl;

    public function __construct($requiresAdmin, $requiresAuth, $title, $route, $routes) {
        $this->requiresAdmin = $requiresAdmin;
        $this->requiresAuth = $requiresAuth;
        $this->title = $title;
        $this->route = $route;
        $this->routes = $routes;
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
        return $this->requiresAuth || $this->requiresAdmin;
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

    /**
     * @return mixed
     */
    public function getRoutes() {
        return$this->routes;
    }

    public function getClass($route) {
        $ext = $this->externalUrl ? 'external' : '';

        if ($route === $this->route || in_array($route, $this->routes)) {
            return $ext . ' active';
        } else {
            return $ext . '';
        }
    }

    /**
     * @return boolean
     */
    public function isExternalUrl() {
        return $this->externalUrl;
    }

    /**
     * @param boolean $externalUrl
     */
    public function setIsExternalUrl($externalUrl) {
        $this->externalUrl = $externalUrl;
    }
}
