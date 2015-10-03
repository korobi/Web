<?php

namespace Korobi\WebBundle\Search;

use JsonSerializable;

class ChannelSuggestion implements JsonSerializable {

    private $channel;
    private $network;
    private $friendlyNetworkName;
    private $channelObjectId;

    /**
     * ChannelSuggestion constructor.
     * @param string $channel The name of the channel.
     * @param string $network The network's slug.
     * @param string $friendlyNetworkName Nicer name for network.
     * @param string $channelObjectId The channel document's ID>
     */
    public function __construct($channel, $network, $friendlyNetworkName, $channelObjectId) {
        $this->channel = $channel;
        $this->network = $network;
        $this->friendlyNetworkName = $friendlyNetworkName;
        $this->channelObjectId = $channelObjectId;
    }

    /**
     * @return string The name of the channel.
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * @return string The network's slug.
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * @return string The channel document's ID>
     */
    public function getChannelObjectId() {
        return $this->channelObjectId;
    }

    /**
     * @return string
     */
    public function getFriendlyNetworkName() {
        return $this->friendlyNetworkName;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return [
            'channel' => $this->channel,
            'network' => $this->network,
            'networkFriendly' => $this->friendlyNetworkName,
            'mongoId' => $this->channelObjectId
        ];
    }
}
