<?php

namespace Korobi\WebBundle\Exception;

class ChannelAccessException extends SecurityException implements CustomPageExceptionInterface {

    const NO_KEY_SUPPLIED = "no_key";
    const INVALID_KEY_SUPPLIED = "invalid_key";

    private $network;
    private $channel;
    private $failureType;

    /**
     * ChannelAccessException constructor.
     * @param string $network
     * @param string $channel
     * @param string $failureType
     */
    public function __construct($network, $channel, $failureType) {
        parent::__construct("You do not have permission to view channel " . $channel . " on network " . $network . ".", 403);
        $this->network = $network;
        $this->channel = $channel;
        $this->failureType = $failureType;
    }

    /**
     * @return string The network to which the channel belonged.
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * @return string The channel the user tried to view.
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * @return string Error page name used to display error details to user.
     */
    public function getViewName() {
        return "channel-access";
    }

    /**
     * @return string
     */
    public function getFailureType() {
        return $this->failureType;
    }
}
