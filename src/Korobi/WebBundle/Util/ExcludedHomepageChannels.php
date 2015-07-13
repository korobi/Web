<?php

namespace Korobi\WebBundle\Util;

class ExcludedHomepageChannels {

    /**
     * @var array Fast storage of the blacklisted items.
     */
    private $items = [];

    /**
     * @param array $config Korobi configuration.
     */
    public function __construct(array $config) {
        foreach ($config['homepage_excluded_channels'] as $item) {
            $this->items[$item['network']][$item['channel']] = true;
        }
    }

    /**
     * Returns whether a network/channel combination is blacklisted
     * such that it will never be chosen to be displayed on the homepage.
     *
     * @param string $network The network slug.
     * @param string $channel The channel name.
     * @return bool
     */
    public function isBlacklisted($network, $channel) {
        return (array_key_exists($network, $this->items) && array_key_exists($channel, $this->items[$network]));
    }
}
