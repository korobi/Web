<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channel_ai",repositoryClass="Korobi\WebBundle\Repository\ChannelAIRepository")
 */
class ChannelAI {

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    private $network;

    /**
     * @MongoDB\String
     */
    private $channel;

    /**
     * @MongoDB\String
     */
    private $join_message;

    /**
     * @MongoDB\Boolean
     */
    private $join_message_enabled;

    /**
     * @MongoDB\Raw
     */
    private $patterns_map;

    /**
     * @MongoDB\Int
     */
    private $pattern_index;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get network
     *
     * @return string $network
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * Set network
     *
     * @param string $network
     * @return self
     */
    public function setNetwork($network) {
        $this->network = $network;
        return $this;
    }

    /**
     * Get channel
     *
     * @return string $channel
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return self
     */
    public function setChannel($channel) {
        $this->channel = $channel;
        return $this;
    }

    /**
     * Get joinMessage
     *
     * @return string $joinMessage
     */
    public function getJoinMessage() {
        return $this->join_message;
    }

    /**
     * Set joinMessage
     *
     * @param string $joinMessage
     * @return self
     */
    public function setJoinMessage($joinMessage) {
        $this->join_message = $joinMessage;
        return $this;
    }

    /**
     * Get joinMessageEnabled
     *
     * @return boolean $joinMessageEnabled
     */
    public function getJoinMessageEnabled() {
        return $this->join_message_enabled;
    }

    /**
     * Set joinMessageEnabled
     *
     * @param boolean $joinMessageEnabled
     * @return self
     */
    public function setJoinMessageEnabled($joinMessageEnabled) {
        $this->join_message_enabled = $joinMessageEnabled;
        return $this;
    }

    /**
     * Get patternsMap
     *
     * @return raw $patternsMap
     */
    public function getPatternsMap() {
        return $this->patterns_map;
    }

    /**
     * Set patternsMap
     *
     * @param raw $patternsMap
     * @return self
     */
    public function setPatternsMap($patternsMap) {
        $this->patterns_map = $patternsMap;
        return $this;
    }

    /**
     * Get patternIndex
     *
     * @return int $patternIndex
     */
    public function getPatternIndex() {
        return $this->pattern_index;
    }

    /**
     * Set patternIndex
     *
     * @param int $patternIndex
     * @return self
     */
    public function setPatternIndex($patternIndex) {
        $this->pattern_index = $patternIndex;
        return $this;
    }
}
