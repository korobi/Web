<?php

namespace Korobi\WebBundle\Parser;

class ChatMessage implements \JsonSerializable {

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

    public function jsonSerialize() {
        return [
            'timestamp'  => $this->timestamp,
            'role'       => $this->role,
            'nickColour' => $this->nickColour,
            'nick'       => $this->nick,
            'message'    => $this->message
        ];
    }

    /**
     * @return string
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getNick() {
        return $this->nick;
    }

    /**
     * @return string
     */
    public function getNickColour() {
        return $this->nickColour;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

}
