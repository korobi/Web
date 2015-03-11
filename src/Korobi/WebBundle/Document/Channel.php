<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="channels",repositoryClass="Korobi\WebBundle\Repository\ChannelRepository")
 */
class Channel {

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
     * @MongoDB\Collection
     */
    private $managers;

    /**
     * @MongoDB\String(nullable=true)
     */
    private $key;

    /**
     * @MongoDB\String
     */
    private $command_prefix;

    /**
     * @MongoDB\Boolean
     */
    private $logs_enabled;

    /**
     * @MongoDB\Boolean
     */
    private $commands_enabled;

    /**
     * @MongoDB\Boolean
     */
    private $punishments_enabled;

    /**
     * @MongoDB\Collection
     */
    private $permissions;

    /**
     * @MongoDB\Raw
     */
    private $account_permissions;

    /**
     * @MongoDB\Raw
     */
    private $settings;

    /**
     * @MongoDB\String
     */
    private $commands_link;

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
     * Get accountPermissions
     *
     * @return raw $accountPermissions
     */
    public function getAccountPermissions() {
        return $this->account_permissions;
    }

    /**
     * Set accountPermissions
     *
     * @param raw $accountPermissions
     * @return self
     */
    public function setAccountPermissions($accountPermissions) {
        $this->account_permissions = $accountPermissions;
        return $this;
    }

    /**
     * Get settings
     *
     * @return raw $settings
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * Set settings
     *
     * @param raw $settings
     * @return self
     */
    public function setSettings($settings) {
        $this->settings = $settings;
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
}
