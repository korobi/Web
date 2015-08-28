<?php

namespace Korobi\WebBundle\Exception;

class ChannelAccessException extends SecurityException {

    private $network;
    private $channel;

    /**
     * ChannelAccessException constructor.
     * @param string $network
     * @param string $channel
     */
    public function __construct($network, $channel) {
        $this->network = $network;
        $this->channel = $channel;
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
}
