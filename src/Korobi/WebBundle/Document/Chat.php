<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="chats",repositoryClass="Korobi\WebBundle\Repository\ChatRepository")
 */
class Chat {

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
     * @MongoDB\Date
     */
    private $date;

    /**
     * @MongoDB\String
     */
    private $message;

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
     * Get date
     *
     * @return date $date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    /**
     * Get message
     *
     * @return string $message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return self
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
}
