<?php
namespace Korobi\WebBundle\Util;

interface IExcludedHomepageChannels {
    /**
     * Returns whether a network/channel combination is blacklisted
     * such that it will never be chosen to be displayed on the homepage.
     *
     * @param string $network The network slug.
     * @param string $channel The channel name.
     * @return bool
     */
    public function isBlacklisted($network, $channel);
}
