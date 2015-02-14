<?php


namespace Korobi\WebBundle\Util;


/**
 * Holds many navigation items.
 * @package Korobi\Util
 */
class NavigationCollection {

    private $items = ["secondary" => [], "primary" => [], "footer" => []];

    public function __construct(array $korobiConfig) {
        $items = $korobiConfig['navigation']['items'];
        array_walk($items, function($value, $key) {
            $this->items[$value['type']][] = new NavigationItem($value['requires_admin'], $value['requires_auth'], $value['title'], $value['route']);
        });
    }

    public function getPrimary() {
        return $this->items['primary'];
    }

    public function getSecondary() {
        return $this->items['secondary'];
    }

    public function getFooter() {
        return $this->items['footer'];
    }
}
