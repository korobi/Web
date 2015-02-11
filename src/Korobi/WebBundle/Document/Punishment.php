<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="punishments",repositoryClass="Korobi\WebBundle\Repository\PunishmentRepository")
 */
class Punishment {

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
    private $expire_date;

    /**
     * @MongoDB\Boolean
     */
    private $expired;

    /**
     * @MongoDB\String
     */
    private $reason;

    /**
     * @MongoDB\String
     */
    private $victim_name;

    /**
     * @MongoDB\String
     */
    private $victim_hostname;

    /**
     * @MongoDB\String
     */
    private $actor_name;

    /**
     * @MongoDB\String
     */
    private $actor_hostname;

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
     * Get expireDate
     *
     * @return string $expireDate
     */
    public function getExpireDate() {
        return $this->expire_date;
    }

    /**
     * Set expireDate
     *
     * @param string $expireDate
     * @return self
     */
    public function setExpireDate($expireDate) {
        $this->expire_date = $expireDate;
        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean $expired
     */
    public function getExpired() {
        return $this->expired;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return self
     */
    public function setExpired($expired) {
        $this->expired = $expired;
        return $this;
    }

    /**
     * Get reason
     *
     * @return string $reason
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return self
     */
    public function setReason($reason) {
        $this->reason = $reason;
        return $this;
    }

    /**
     * Get victimName
     *
     * @return string $victimName
     */
    public function getVictimName() {
        return $this->victim_name;
    }

    /**
     * Set victimName
     *
     * @param string $victimName
     * @return self
     */
    public function setVictimName($victimName) {
        $this->victim_name = $victimName;
        return $this;
    }

    /**
     * Get victimHostname
     *
     * @return string $victimHostname
     */
    public function getVictimHostname() {
        return $this->victim_hostname;
    }

    /**
     * Set victimHostname
     *
     * @param string $victimHostname
     * @return self
     */
    public function setVictimHostname($victimHostname) {
        $this->victim_hostname = $victimHostname;
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
}
