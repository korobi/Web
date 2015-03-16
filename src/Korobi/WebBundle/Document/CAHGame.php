<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="cah_games",repositoryClass="Korobi\WebBundle\Repository\CAHGameRepository")
 */
class CAHGame {
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
    private $state;

    /**
     * @MongoDB\Collection
     */
    private $packs;

    /**
     * @MongoDB\Collection
     */
    private $lifetime_packs;

    /**
     * @MongoDB\String
     */
    private $host;

    /**
     * @MongoDB\Collection
     */
    private $lifetime_hosts;

    /**
     * @MongoDB\Collection
     */
    private $players;

    /**
     * @MongoDB\Collection
     */
    private $lifetime_players;

    /**
     * @MongoDB\Date
     */
    private $start_time;

    /**
     * @MongoDB\Date
     */
    private $end_time;

    /**
     * @MongoDB\Raw
     */
    private $end_scores;

    /**
     * @MongoDB\Raw
     */
    private $card_counts;

    /**
     * @MongoDB\Raw
     */
    private $rounds;

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
     * Get state
     *
     * @return string $state
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return self
     */
    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    /**
     * Get packs
     *
     * @return collection $packs
     */
    public function getPacks() {
        return $this->packs;
    }

    /**
     * Set packs
     *
     * @param collection $packs
     * @return self
     */
    public function setPacks($packs) {
        $this->packs = $packs;
        return $this;
    }

    /**
     * Get lifetimePacks
     *
     * @return collection $lifetimePacks
     */
    public function getLifetimePacks() {
        return $this->lifetime_packs;
    }

    /**
     * Set lifetimePacks
     *
     * @param collection $lifetimePacks
     * @return self
     */
    public function setLifetimePacks($lifetimePacks) {
        $this->lifetime_packs = $lifetimePacks;
        return $this;
    }

    /**
     * Get host
     *
     * @return string $host
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return self
     */
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * Get lifetimeHosts
     *
     * @return collection $lifetimeHosts
     */
    public function getLifetimeHosts() {
        return $this->lifetime_hosts;
    }

    /**
     * Set lifetimeHosts
     *
     * @param collection $lifetimeHosts
     * @return self
     */
    public function setLifetimeHosts($lifetimeHosts) {
        $this->lifetime_hosts = $lifetimeHosts;
        return $this;
    }

    /**
     * Get players
     *
     * @return collection $players
     */
    public function getPlayers() {
        return $this->players;
    }

    /**
     * Set players
     *
     * @param collection $players
     * @return self
     */
    public function setPlayers($players) {
        $this->players = $players;
        return $this;
    }

    /**
     * Get lifetimePlayers
     *
     * @return collection $lifetimePlayers
     */
    public function getLifetimePlayers() {
        return $this->lifetime_players;
    }

    /**
     * Set lifetimePlayers
     *
     * @param collection $lifetimePlayers
     * @return self
     */
    public function setLifetimePlayers($lifetimePlayers) {
        $this->lifetime_players = $lifetimePlayers;
        return $this;
    }

    /**
     * Get startTime
     *
     * @return date $startTime
     */
    public function getStartTime() {
        return $this->start_time;
    }

    /**
     * Set startTime
     *
     * @param date $startTime
     * @return self
     */
    public function setStartTime($startTime) {
        $this->start_time = $startTime;
        return $this;
    }

    /**
     * Get endTime
     *
     * @return date $endTime
     */
    public function getEndTime() {
        return $this->end_time;
    }

    /**
     * Set endTime
     *
     * @param date $endTime
     * @return self
     */
    public function setEndTime($endTime) {
        $this->end_time = $endTime;
        return $this;
    }

    /**
     * Get endScores
     *
     * @return raw $endScores
     */
    public function getEndScores() {
        return $this->end_scores;
    }

    /**
     * Set endScores
     *
     * @param raw $endScores
     * @return self
     */
    public function setEndScores($endScores) {
        $this->end_scores = $endScores;
        return $this;
    }

    /**
     * Get cardCounts
     *
     * @return raw $cardCounts
     */
    public function getCardCounts() {
        return $this->card_counts;
    }

    /**
     * Set cardCounts
     *
     * @param raw $cardCounts
     * @return self
     */
    public function setCardCounts($cardCounts) {
        $this->card_counts = $cardCounts;
        return $this;
    }

    /**
     * Get rounds
     *
     * @return raw $rounds
     */
    public function getRounds() {
        return $this->rounds;
    }

    /**
     * Set rounds
     *
     * @param raw $rounds
     * @return self
     */
    public function setRounds($rounds) {
        $this->rounds = $rounds;
        return $this;
    }
}
