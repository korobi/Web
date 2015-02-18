<?php

namespace Korobi\WebBundle\Util;

/**
 * A 'Plain Old PHP Object' for navigation items.
 *
 * @package Korobi\WebBundle\Util
 */
class NavigationItem {

    private $title;
    private $route;
    private $routes;
    private $requiresAuth;
    private $requiresAdmin;
    private $externalUrl;

    /**
     * @param $title
     * @param $route
     * @param $routes
     * @param $requiresAuth
     * @param $requiresAdmin
     * @param $externalUrl
     */
    public function __construct($title, $route, $routes, $requiresAuth, $requiresAdmin, $externalUrl) {
        $this->title = $title;
        $this->route = $route;
        $this->routes = $routes;
        $this->requiresAuth = $requiresAuth;
        $this->requiresAdmin = $requiresAdmin;
        $this->externalUrl = $externalUrl;
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
        return $this->routes;
    }

    /**
     * @return boolean
     */
    public function getRequiresAuth() {
        return $this->requiresAuth || $this->requiresAdmin;
    }

    /**
     * @return boolean Whether or not the visibility of this item requires authentication.
     */
    public function getRequiresAdmin() {
        return $this->requiresAdmin;
    }

    /**
     * @return mixed
     */
    public function isExternalUrl() {
        return $this->externalUrl;
    }

    /**
     * @param $route
     * @return string
     */
    public function getClass($route) {
        $ext = $this->externalUrl ? 'external' : '';

        if ($route === $this->route || in_array($route, $this->routes)) {
            return $ext . ' active';
        } else {
            return $ext . '';
        }
    }
}
