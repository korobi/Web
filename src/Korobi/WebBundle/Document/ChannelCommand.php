<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channel_commands",repositoryClass="Korobi\WebBundle\Repository\ChannelCommandRepository")
 */
class ChannelCommand {

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
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\Collection
     */
    private $value;

    /**
     * @MongoDB\Field(type="boolean")
     */
    private $is_action;

    /**
     * @MongoDB\Field(type="boolean")
     */
    private $is_alias;

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
     * Get name
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get value
     *
     * @return collection $value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param collection $value
     * @return self
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Get isAction
     *
     * @return boolean $isAction
     */
    public function getIsAction() {
        return $this->is_action;
    }

    /**
     * Set isAction
     *
     * @param boolean $isAction
     * @return self
     */
    public function setIsAction($isAction) {
        $this->is_action = $isAction;
        return $this;
    }

    /**
     * Get isAlias
     *
     * @return boolean $isAlias
     */
    public function getIsAlias() {
        return $this->is_alias;
    }

    /**
     * Set isAlias
     *
     * @param boolean $isAlias
     * @return self
     */
    public function setIsAlias($isAlias) {
        $this->is_alias = $isAlias;
        return $this;
    }
}
