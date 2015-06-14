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
     * @MongoDB\Date
     */
    private $start_time;

    /**
     * @MongoDB\String
     */
    private $state;

    /**
     * @MongoDB\Collection
     */
    private $card_packs;

    /**
     * @MongoDB\Collection
     */
    private $card_packs_lifetime;

    /**
     * @MongoDB\String
     */
    private $host;

    /**
     * @MongoDB\Collection
     */
    private $hosts_lifetime;

    /**
     * @MongoDB\Collection
     */
    private $players;

    /**
     * @MongoDB\Collection
     */
    private $players_lifetime;

    /**
     * @MongoDB\Collection
     */
    private $house_rules;

    /**
     * @MongoDB\Collection
     */
    private $house_rules_lifetime;

    /**
     * @MongoDB\Boolean
     */
    private $start_rushed;

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
     * Get cardPacks
     *
     * @return collection $cardPacks
     */
    public function getCardPacks() {
        return $this->card_packs;
    }

    /**
     * Set cardPacks
     *
     * @param collection $cardPacks
     * @return self
     */
    public function setCardPacks($cardPacks) {
        $this->card_packs = $cardPacks;
        return $this;
    }

    /**
     * Get cardPacksLifetime
     *
     * @return collection $cardPacksLifetime
     */
    public function getCardPacksLifetime() {
        return $this->card_packs_lifetime;
    }

    /**
     * Set cardPacksLifetime
     *
     * @param collection $cardPacksLifetime
     * @return self
     */
    public function setCardPacksLifetime($cardPacksLifetime) {
        $this->card_packs_lifetime = $cardPacksLifetime;
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
     * Get hostsLifetime
     *
     * @return collection $hostsLifetime
     */
    public function getHostsLifetime() {
        return $this->hosts_lifetime;
    }

    /**
     * Set hostsLifetime
     *
     * @param collection $hostsLifetime
     * @return self
     */
    public function setHostsLifetime($hostsLifetime) {
        $this->hosts_lifetime = $hostsLifetime;
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
     * Get playersLifetime
     *
     * @return collection $playersLifetime
     */
    public function getPlayersLifetime() {
        return $this->players_lifetime;
    }

    /**
     * Set playersLifetime
     *
     * @param collection $playersLifetime
     * @return self
     */
    public function setPlayersLifetime($playersLifetime) {
        $this->players_lifetime = $playersLifetime;
        return $this;
    }

    /**
     * Get houseRules
     *
     * @return collection $houseRules
     */
    public function getHouseRules() {
        return $this->house_rules;
    }

    /**
     * Set houseRules
     *
     * @param collection $houseRules
     * @return self
     */
    public function setHouseRules($houseRules) {
        $this->house_rules = $houseRules;
        return $this;
    }

    /**
     * Get houseRulesLifetime
     *
     * @return collection $houseRulesLifetime
     */
    public function getHouseRulesLifetime() {
        return $this->house_rules_lifetime;
    }

    /**
     * Set houseRulesLifetime
     *
     * @param collection $houseRulesLifetime
     * @return self
     */
    public function setHouseRulesLifetime($houseRulesLifetime) {
        $this->house_rules_lifetime = $houseRulesLifetime;
        return $this;
    }

    /**
     * Get startRushed
     *
     * @return boolean $startRushed
     */
    public function getStartRushed() {
        return $this->start_rushed;
    }

    /**
     * Set startRushed
     *
     * @param boolean $startRushed
     * @return self
     */
    public function setStartRushed($startRushed) {
        $this->start_rushed = $startRushed;
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
