<?php

namespace Korobi\WebBundle\Util;

/**
 * Holds many navigation items.
 * @package Korobi\Util
 */
class NavigationCollection {

    private $items = [
        'primary' => [],
        'secondary' => [],
        'footer' => []
    ];

    public function __construct(array $config) {
        $items = $config['navigation']['items'];
        array_walk($items, function($value, $key) {
            $this->items[$value['type']][] = new NavigationItem(
                $value['title'],
                $value['route'],
                $value['routes'],
                $value['requires_auth'],
                $value['requires_admin'],
                $value['external']
            );
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
