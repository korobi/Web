<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="chat_indexes",repositoryClass="Korobi\WebBundle\Repository\ChatIndexRepository")
 */
class ChatIndex {

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
     * @MongoDB\Field(type="int")
     */
    private $year;

    /**
     * @MongoDB\Field(type="int")
     */
    private $day_of_year;

    /**
     * @MongoDB\Field(type="boolean")
     */
    private $has_valid_content;

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
     * Get year
     *
     * @return int $year
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Set year
     *
     * @param int $year
     * @return self
     */
    public function setYear($year) {
        $this->year = $year;
        return $this;
    }

    /**
     * Get dayOfYear
     *
     * @return int $dayOfYear
     */
    public function getDayOfYear() {
        return $this->day_of_year;
    }

    /**
     * Set dayOfYear
     *
     * @param int $dayOfYear
     * @return self
     */
    public function setDayOfYear($dayOfYear) {
        $this->day_of_year = $dayOfYear;
        return $this;
    }

    /**
     * Get hasValidContent
     *
     * @return boolean $hasValidContent
     */
    public function getHasValidContent() {
        return $this->has_valid_content;
    }

    /**
     * Set hasValidContent
     *
     * @param boolean $hasValidContent
     * @return self
     */
    public function setHasValidContent($hasValidContent) {
        $this->has_valid_content = $hasValidContent;
        return $this;
    }
}
