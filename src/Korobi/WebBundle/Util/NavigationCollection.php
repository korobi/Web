<?php

namespace Korobi\WebBundle\Util;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Holds many NavigationItems.
 *
 * @package Korobi\WebBundle\Util
 */
class NavigationCollection {

    private $items = [
        'primary' => [],
        'secondary' => [],
        'footer' => [],
    ];

    public function __construct(array $config, TranslatorInterface $translator) {
        $items = $config['navigation']['items'];
        array_walk($items, function($value, $key) use ($translator) {
            $navItem = new NavigationItem(
                $value['title'],
                $value['route'],
                $value['routes'],
                $value['requires_auth'],
                $value['requires_admin'],
                $value['external']
            );
            $navItem->setTranslator($translator);
            $this->items[$value['type']][] = $navItem;
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
