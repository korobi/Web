<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channel_statistics",repositoryClass="Korobi\WebBundle\Repository\StatisticRepository")
 */
class Statistic {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $network;

    /**
     * @MongoDB\Field(type="string")
     */
    private $channel;

    /**
     * @MongoDB\Raw
     */
    private $current_counts;

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
     * Get currentCounts
     *
     * @return raw $currentCounts
     */
    public function getCurrentCounts() {
        return $this->current_counts;
    }

    /**
     * Set currentCounts
     *
     * @param raw $currentCounts
     * @return self
     */
    public function setCurrentCounts($currentCounts) {
        $this->current_counts = $currentCounts;
        return $this;
    }
}
