<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channel_config",repositoryClass="Korobi\WebBundle\Repository\ChannelConfigRepository")
 */
class ChannelConfig {
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
    private $key;

    /**
     * @MongoDB\Boolean
     */
    private $logs_enabled;

    /**
     * @MongoDB\Boolean
     */
    private $commands_enabled;

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
     * Get key
     *
     * @return string $key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * Get logsEnabled
     *
     * @return boolean $logsEnabled
     */
    public function getLogsEnabled() {
        return $this->logs_enabled;
    }

    /**
     * Set logsEnabled
     *
     * @param boolean $logsEnabled
     * @return self
     */
    public function setLogsEnabled($logsEnabled) {
        $this->logs_enabled = $logsEnabled;
        return $this;
    }

    /**
     * Get commandsEnabled
     *
     * @return boolean $commandsEnabled
     */
    public function getCommandsEnabled() {
        return $this->commands_enabled;
    }

    /**
     * Set commandsEnabled
     *
     * @param boolean $commandsEnabled
     * @return self
     */
    public function setCommandsEnabled($commandsEnabled) {
        $this->commands_enabled = $commandsEnabled;
        return $this;
    }
}
