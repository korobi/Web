<?php

namespace Korobi\WebBundle\Exception;

class FeatureNotEnabledException extends \Exception {

    private $network;
    private $channel;
    private $feature;

    /**
     * FeatureNotEnabledException constructor.
     * @param string $network
     * @param string $channel
     * @param string $feature
     */
    public function __construct($network, $channel, $feature) {
        $this->network = $network;
        $this->channel = $channel;
        $this->feature = $feature;
    }

    /**
     * @return string
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * @return string
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getFeature() {
        return $this->feature;
    }
}
