<?php

namespace Korobi\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChannelViewKey
 */
class ChannelViewKey {
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $viewKey;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get network
     *
     * @return string
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * Set network
     *
     * @param string $network
     * @return ChannelViewKey
     */
    public function setNetwork($network) {
        $this->network = $network;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return ChannelViewKey
     */
    public function setChannel($channel) {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get viewKey
     *
     * @return string
     */
    public function getViewKey() {
        return $this->viewKey;
    }

    /**
     * Set viewKey
     *
     * @param string $viewKey
     * @return ChannelViewKey
     */
    public function setViewKey($viewKey) {
        $this->viewKey = $viewKey;

        return $this;
    }
}
