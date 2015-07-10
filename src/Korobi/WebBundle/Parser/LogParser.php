<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Document\Chat;

class LogParser {

    const JOIN_USER_PREFIX = '-->';
    const PART_USER_PREFIX = '<--';
    const ACTION_USER_PREFIX = '*';
    const ACTION_SERVER_PREFIX = '--';
    const ACTION_SERVER_COLOUR = '14';

    // -----------------
    // ---- Parsing ----
    // -----------------

    /**
     * @param Chat $chat
     * @return string
     */
    // @Kashike flails
    public static function parseAction(Chat $chat) {
        $result = '';

        $result .= self::getSpanForColour(
            self::getColourForActor($chat),
            self::transformActor($chat->getActorName(), $chat->getActorPrefix())
        );
        $result .= ' ';
        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // @Kashike joined the channel
    public static function parseJoin(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' (';
        $result .= IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result .= ') ';
        $result .= 'joined the channel';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // @lol768 was kicked by @Kashike (hello)
    public static function parseKick(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getRecipientName(), $chat->getRecipientPrefix());
        $result .= ' was kicked by ';
        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' (' . $chat->getMessage() . ')';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // meow!
    public static function parseMessage(Chat $chat) {
        return IRCTextParser::parse($chat->getMessage());
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // Server sets mode +CQnst
    // @Kashike sets mode +b *!*@test.com
    public static function parseMode(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' sets mode ' . $chat->getMessage();

        if ($chat->getRecipientPrefix() !== null) {
            $result .= self::transformUserModeToLetter($chat->getRecipientPrefix());
            $result .= ' ';
            $result .= self::transformActor($chat->getRecipientName());
        } else if ($chat->getChannelMode() !== null) {
            $result .= self::transformChannelModeToLetter($chat->getChannelMode());
            $result .= ' ';
            $result .= self::transformActor($chat->getRecipientHostname());
        }

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** @drtshock is now known as @Trent
    public static function parseNick(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' is now known as ';
        $result .= self::transformActor($chat->getRecipientName(), $chat->getActorPrefix());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parsePart(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' (';
        $result .= IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result .= ') ';
        $result .= 'left the channel';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parseQuit(Chat $chat) {
        $result = '';

        $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
        $result .= ' (';
        $result .= IRCTextParser::createHostnameTag($chat->getActorHostname());
        $result .= ') ';
        $result .= 'has quit (' . $chat->getMessage() . ')';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parseTopic(Chat $chat) {
        $result = '';

        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= 'Topic is: ' . IRCTextParser::parse($chat->getMessage());
        } else {
            $result .= self::transformActor($chat->getActorName(), $chat->getActorPrefix());
            $result .= ' has changed the topic to: ' . IRCTextParser::parse($chat->getMessage());
        }

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function getColourForActor(Chat $chat) {
        switch ($chat->getType()) {
            case 'ACTION':
            case 'MESSAGE':
                return NickColours::getColourForNick(self::transformActor($chat->getActorName()));
            case 'PART':
            case 'QUIT':
                return '04';
            case 'JOIN':
                return '03';
            default:
                return self::ACTION_SERVER_COLOUR;
        }
    }

    /**
     * Returns the name to display for that chat entry.
     *
     * @param Chat $chat
     * @return string
     */
    public static function getDisplayName(Chat $chat) {
        switch ($chat->getType()) {
            case 'MESSAGE':
                return $chat->getActorName();
            case 'ACTION':
                return self::ACTION_USER_PREFIX;
            case 'JOIN':
                return self::JOIN_USER_PREFIX;
            case 'PART':
            case 'QUIT':
                return self::PART_USER_PREFIX;
            default:
                return self::ACTION_SERVER_PREFIX;
        }
    }

    /**
     * Returns the nickname of the actor for that chat entry or its hostname
     * in case there is no nickname.
     *
     * @param Chat $chat
     * @return string
     */
    public static function getActorName(Chat $chat) {
        return $chat->getActorName() == Chat::ACTOR_INTERNAL
            ? self::transformActor($chat->getActorHostname())
            : $chat->getActorName();
    }

    // -----------------
    // ---- Helpers ----
    // -----------------

    /**
     * @param $colour
     * @param $text
     * @return string
     */
    private static function getSpanForColour($colour, $text) {
        return IRCTextParser::createColorTag($colour, IRCTextParser::DEFAULT_BACKGROUND)
            . $text . IRCTextParser::closeTag();
    }

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @param $prefix
     * @return string
     */
    protected static function transformActor($actor, $prefix = '') {
        if ($actor == Chat::ACTOR_INTERNAL) {
            return 'Server';
        }

        if(empty($prefix) || $prefix == 'NORMAL') {
            return $actor;
        }

        return '<span class="' . strtolower($prefix) . '">' . $actor . '</span>';
    }

    /**
     * @param $mode
     * @return string
     */
    private static function transformChannelModeToLetter($mode) {
        switch ($mode) {
            case 'BAN':
                return 'b';
            case 'QUIET':
                return 'q';
            case 'NORMAL':
            default:
                return '';
        }
    }

    /**
     * @param $mode
     * @return string
     */
    private static function transformUserModeToLetter($mode) {
        switch ($mode) {
            case 'OWNER':
                return 'q';
            case 'ADMIN':
                return 'a';
            case 'OPERATOR':
                return 'o';
            case 'HALF_OP':
                return 'h';
            case 'VOICE':
                return 'v';
            case 'NORMAL':
            default:
                return '';
        }
    }
}
