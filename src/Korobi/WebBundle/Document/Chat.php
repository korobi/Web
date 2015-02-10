<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="chats",repositoryClass="Korobi\WebBundle\Repository\ChatRepository")
 */
class Chat {

    const ACTOR_INTERNAL = '$Internal';

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
    private $type;

    /**
     * @MongoDB\Date
     */
    private $date;

    /**
     * @MongoDB\String
     */
    private $actor_name;

    /**
     * @MongoDB\String
     */
    private $actor_hostname;

    /**
     * @MongoDB\String
     */
    private $actor_prefix;

    /**
     * @MongoDB\String
     */
    private $recipient_name;

    /**
     * @MongoDB\String
     */
    private $recipient_hostname;

    /**
     * @MongoDB\String
     */
    private $recipient_prefix;

    /**
     * @MongoDB\String
     */
    private $channel_mode;

    /**
     * @MongoDB\Boolean
     */
    private $imported;

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
     * Get type
     *
     * @return string $type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type) {
        $this->type = $type;
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
     * Get actorName
     *
     * @return string $actorName
     */
    public function getActorName() {
        return $this->actor_name;
    }

    /**
     * Set actorName
     *
     * @param string $actorName
     * @return self
     */
    public function setActorName($actorName) {
        $this->actor_name = $actorName;
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
     * Get actorPrefix
     *
     * @return string $actorPrefix
     */
    public function getActorPrefix() {
        return $this->actor_prefix;
    }

    /**
     * Set actorPrefix
     *
     * @param string $actorPrefix
     * @return self
     */
    public function setActorPrefix($actorPrefix) {
        $this->actor_prefix = $actorPrefix;
        return $this;
    }

    /**
     * Get recipientName
     *
     * @return string $recipientName
     */
    public function getRecipientName() {
        return $this->recipient_name;
    }

    /**
     * Set recipientName
     *
     * @param string $recipientName
     * @return self
     */
    public function setRecipientName($recipientName) {
        $this->recipient_name = $recipientName;
        return $this;
    }

    /**
     * Get recipientHostname
     *
     * @return string $recipientHostname
     */
    public function getRecipientHostname() {
        return $this->recipient_hostname;
    }

    /**
     * Set recipientHostname
     *
     * @param string $recipientHostname
     * @return self
     */
    public function setRecipientHostname($recipientHostname) {
        $this->recipient_hostname = $recipientHostname;
        return $this;
    }

    /**
     * Get recipientPrefix
     *
     * @return string $recipientPrefix
     */
    public function getRecipientPrefix() {
        return $this->recipient_prefix;
    }

    /**
     * Set recipientPrefix
     *
     * @param string $recipientPrefix
     * @return self
     */
    public function setRecipientPrefix($recipientPrefix) {
        $this->recipient_prefix = $recipientPrefix;
        return $this;
    }

    /**
     * Get channelMode
     *
     * @return string $channelMode
     */
    public function getChannelMode() {
        return $this->channel_mode;
    }

    /**
     * Set channelMode
     *
     * @param string $channelMode
     * @return self
     */
    public function setChannelMode($channelMode) {
        $this->channel_mode = $channelMode;
        return $this;
    }

    /**
     * Get imported
     *
     * @return boolean $imported
     */
    public function getImported() {
        return $this->imported;
    }

    /**
     * Set imported
     *
     * @param boolean $imported
     * @return self
     */
    public function setImported($imported) {
        $this->imported = $imported;
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
