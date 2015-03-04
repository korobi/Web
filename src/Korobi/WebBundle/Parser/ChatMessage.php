<?php

namespace Korobi\WebBundle\Parser;

class ChatMessage {

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $nickColour;

    /**
     * @var string
     */
    private $nick;

    /**
     * @var string
     */
    private $message;

    public function __construct(\DateTime $date, $role, $colour, $actor, $message) {
        $this->timestamp = date('H:i:s', $date->getTimestamp());
        $this->role = strtolower($role);
        $this->nickColour = $colour;
        $this->nick = $actor;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public getRole() {
        return $this->role;
    }

    /**
     * @return string
     */
    public getNick() {
        return $this->nick;
    }

    /**
     * @return string
     */
    public getNickColour() {
        return $this->nickColour;
    }

    /**
     * @return string
     */
    public getMessage() {
        return $this->message;
    }

}
