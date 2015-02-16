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

    public function __construct(array $korobiConfig) {
        $items = $korobiConfig['navigation']['items'];
        array_walk($items, function($value, $key) {
            $item = new NavigationItem($value['requires_admin'], $value['requires_auth'], $value['title'], $value['route'], $value['routes']);
            $item->setIsExternalUrl($value['external']);
            $this->items[$value['type']][] = $item;
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
