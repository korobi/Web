<?php

namespace Korobi\WebBundle\IRC\Parser;

use Korobi\WebBundle\Document\Chat;

interface LogParserInterface {
    
    /**
     * @param Chat $chat
     * @return string
     */
    public function parseAction(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseJoin(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseKick(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseMessage(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseMode(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseNick(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parsePart(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseQuit(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function parseTopic(Chat $chat);

    /**
     * @param Chat $chat
     * @return string
     */
    public function getColourForActor(Chat $chat);

    /**
     * Returns the name to display for that chat entry.
     *
     * @param Chat $chat
     * @return string
     */
    public function getDisplayName(Chat $chat);

    /**
     * Returns the nickname of the actor for that chat entry or its hostname
     * in case there is no nickname.
     *
     * @param Chat $chat
     * @return string
     */
    public function getActorName(Chat $chat);

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @param $prefix
     * @return string
     */
    public function transformActor($actor, $prefix = '');
}
