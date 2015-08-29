<?php

namespace Korobi\WebBundle\Exception;

class ChannelAccessException extends SecurityException implements CustomPageExceptionInterface {

    private $network;
    private $channel;

    /**
     * ChannelAccessException constructor.
     * @param string $network
     * @param string $channel
     */
    public function __construct($network, $channel) {
        // misuse of exception code system?
        parent::__construct("You do not have permission to view channel " . $channel . " on network " . $network . ".", 403);
        $this->network = $network;
        $this->channel = $channel;
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
}
