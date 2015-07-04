<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channels",repositoryClass="Korobi\WebBundle\Repository\ChannelRepository")
 */
class Channel {

    /**
     * @MongoDB\Id
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
     * @MongoDB\String(nullable=true)
     */
    private $key;

    /**
     * @MongoDB\Collection
     */
    private $managers;

    /**
     * @MongoDB\Boolean
     */
    private $logs_enabled;

    /**
     * @MongoDB\Boolean
     */
    private $commands_enabled;

    /**
     * @MongoDB\String
     */
    private $command_prefix;

    /**
     * @MongoDB\String
     */
    private $commands_link;

    /**
     * @MongoDB\Boolean
     */
    private $punishments_enabled;

    /**
     * @MongoDB\Collection
     */
    private $repositories;

    /**
     * @MongoDB\Date
     */
    private $last_activity;

    /**
     * @MongoDB\Date
     */
    private $last_activity_valid;

    /**
     * @MongoDB\Collection
     */
    private $permissions;

    /**
     * @MongoDB\Raw
     */
    private $topic;

    /**
     * @MongoDB\Boolean
     */
    private $kitty_image;

    /**
     * @MongoDB\Boolean
     */
    private $meow_module_enabled;


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
     * Get key
     *
     * @return string $key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * Get managers
     *
     * @return collection $managers
     */
    public function getManagers() {
        return $this->managers;
    }

    /**
     * Set managers
     *
     * @param collection $managers
     * @return self
     */
    public function setManagers($managers) {
        $this->managers = $managers;
        return $this;
    }

    /**
     * Get logsEnabled
     *
     * @return boolean $logsEnabled
     */
    public function getLogsEnabled() {
        return $this->logs_enabled;
    }

    /**
     * Set logsEnabled
     *
     * @param boolean $logsEnabled
     * @return self
     */
    public function setLogsEnabled($logsEnabled) {
        $this->logs_enabled = $logsEnabled;
        return $this;
    }

    /**
     * Get commandsEnabled
     *
     * @return boolean $commandsEnabled
     */
    public function getCommandsEnabled() {
        return $this->commands_enabled;
    }

    /**
     * Set commandsEnabled
     *
     * @param boolean $commandsEnabled
     * @return self
     */
    public function setCommandsEnabled($commandsEnabled) {
        $this->commands_enabled = $commandsEnabled;
        return $this;
    }

    /**
     * Get command_prefix
     *
     * @return string $command_prefix
     */
    public function getCommandPrefix() {
        return $this->command_prefix;
    }

    /**
     * Set command_prefix
     *
     * @param string $command_prefix
     * @return self
     */
    public function setCommandPrefix($command_prefix) {
        $this->command_prefix = $command_prefix;
        return $this;
    }

    /**
     * Get commandsLink
     *
     * @return string $commandsLink
     */
    public function getCommandsLink() {
        return $this->commands_link;
    }

    /**
     * Set commandsLink
     *
     * @param string $commandsLink
     * @return self
     */
    public function setCommandsLink($commandsLink) {
        $this->commands_link = $commandsLink;
        return $this;
    }

    /**
     * Get punishmentsEnabled
     *
     * @return boolean $punishmentsEnabled
     */
    public function getPunishmentsEnabled() {
        return $this->punishments_enabled;
    }

    /**
     * Set punishmentsEnabled
     *
     * @param boolean $punishmentsEnabled
     * @return self
     */
    public function setPunishmentsEnabled($punishmentsEnabled) {
        $this->punishments_enabled = $punishmentsEnabled;
        return $this;
    }

    /**
     * Get repositories
     *
     * @return collection $repositories
     */
    public function getRepositories() {
        return $this->repositories;
    }

    /**
     * Set repositories
     *
     * @param collection $repositories
     * @return self
     */
    public function setRepositories($repositories) {
        $this->repositories = $repositories;
        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return date $lastActivity
     */
    public function getLastActivity() {
        return $this->last_activity;
    }

    /**
     * Set lastActivity
     *
     * @param date $lastActivity
     * @return self
     */
    public function setLastActivity($lastActivity) {
        $this->last_activity = $lastActivity;
        return $this;
    }

    /**
     * Get lastActivityValid
     *
     * @return date $lastActivityValid
     */
    public function getLastActivityValid() {
        return $this->last_activity_valid;
    }

    /**
     * Set lastActivityValid
     *
     * @param date $lastActivityValid
     * @return self
     */
    public function setLastActivityValid($lastActivityValid) {
        $this->last_activity_valid = $lastActivityValid;
        return $this;
    }

    /**
     * Get topic
     *
     * @return raw $topic
     */
    public function getTopic() {
        return $this->topic;
    }

    /**
     * Set topic
     *
     * @param raw $topic
     * @return self
     */
    public function setTopic($topic) {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Get permissions
     *
     * @return collection $permissions
     */
    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * Set permissions
     *
     * @param collection $permissions
     * @return self
     */
    public function setPermissions($permissions) {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Get kittyImage
     *
     * @return boolean $kittyImage
     */
    public function getKittyImage() {
        return $this->kitty_image;
    }

    /**
     * Set kittyImage
     *
     * @param boolean $kittyImage
     * @return self
     */
    public function setKittyImage($kittyImage) {
        $this->kitty_image = $kittyImage;
        return $this;
    }

    /**
     * Get meowModuleEnabled
     *
     * @return boolean $meowModuleEnabled
     */
    public function getMeowModuleEnabled() {
        return $this->meow_module_enabled;
    }

    /**
     * Set meowModuleEnabled
     *
     * @param boolean $meowModuleEnabled
     * @return self
     */
    public function setMeowModuleEnabled($meowModuleEnabled) {
        $this->meow_module_enabled = $meowModuleEnabled;
        return $this;
    }
}
