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

        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::getSpanForColour(self::getColourForActor($chat), $chat->getActorName());
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

        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' (';
        $result .= $chat->getActorHostname();
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

        $result .= self::createUserMode($chat->getRecipientPrefix());
        $result .= self::transformActor($chat->getRecipientName());
        $result .= ' was kicked by ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
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

        // mode set by internal actor
        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' sets mode ' . $chat->getMessage();
        } else {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' sets mode ' . $chat->getMessage();
        }

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

        $prefix = self::createUserMode($chat->getActorPrefix());

        $result .= $prefix;
        $result .= $chat->getActorName();
        $result .= ' is now known as ';
        $result .= $prefix;
        $result .= $chat->getRecipientName();

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parsePart(Chat $chat) {
        $result = '';

        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());

        $result .= ' (';
        $result .= $chat->getActorHostname();
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

        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' (';
        $result .= $chat->getActorHostname();
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
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
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
                return '02';
            case 'JOIN':
                return '03';
            default:
                return self::ACTION_SERVER_COLOUR;
        }
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function getActorName(Chat $chat) {
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

    // -----------------
    // ---- Helpers ----
    // -----------------

    /**
     * @param $colour
     * @param $text
     * @return string
     */
    private static function getSpanForColour($colour, $text) {
        return '<span class="irc--' . $colour . '-99">' . self::transformActor($text) . '</span>';
    }

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @return string
     */
    protected static function transformActor($actor) {
        if ($actor == Chat::ACTOR_INTERNAL) {
            return 'Server';
        }

        return $actor;
    }

    /**
     * @param $prefix
     * @return string
     */
    private static function createUserMode($prefix) {
        switch ($prefix) {
            case 'OWNER':
                return '<span class="irc--04-99">~</span>';
            case 'ADMIN':
                return '<span class="irc--11-99">&</span>';
            case 'OPERATOR':
                return '<span class="irc--09-99">@</span>';
            case 'HALF_OP':
                return '<span class="irc--13-99">%</span>';
            case 'VOICE':
                return '<span class="irc--08-99">+</span>';
            case 'NORMAL':
            default:
                return '';
        }
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
