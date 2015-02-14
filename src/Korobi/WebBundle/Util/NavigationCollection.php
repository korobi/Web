<?php


namespace Korobi\WebBundle\Util;


/**
 * Holds many navigation items.
 * @package Korobi\Util
 */
class NavigationCollection {

    private $items = [];

    public function __construct(array $korobiConfig) {
        $items = $korobiConfig['navigation']['items'];
        array_walk($items, function($value, $key) {
            die(json_encode($value));
        });
    }
}
