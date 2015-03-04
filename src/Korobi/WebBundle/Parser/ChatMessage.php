<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Document\Chat;

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

    public __construct(\DateTime $date, $role, $colour, $actor, $message) {
        $this->timestamp = date('H:i:s', $date->getTimestamp());
        $this->role = strtolower($role);
        $this->nickColour = $colour;
        $this->nick = $actor == Chat::ACTOR_INTERNAL ? 'Server' : $actor;
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
