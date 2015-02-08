<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="chats",repositoryClass="Korobi\WebBundle\Repository\ChatRepository")
 */
class Chat {

    const ACTOR_SERVER = '$Server';

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
    private $actor;

    /**
     * @MongoDB\String
     */
    private $actor_hostname;

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
     * Get actor
     *
     * @return string $actor
     */
    public function getActor() {
        return $this->actor;
    }

    /**
     * Set actor
     *
     * @param string $actor
     * @return self
     */
    public function setActor($actor) {
        $this->actor = $actor;
        return $this;
    }

    /**
     * Get actorHostname
     *
     * @return string $actorHostname
     */
    public function getActorHostname() {
        return $this->actor_hostname;
    }

    /**
     * Set actorHostname
     *
     * @param string $actorHostname
     * @return self
     */
    public function setActorHostname($actorHostname) {
        $this->actor_hostname = $actorHostname;
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
