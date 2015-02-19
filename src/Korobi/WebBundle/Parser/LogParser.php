<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Document\Chat;

class LogParser {

    const ACTION_USER_PREFIX = '*';
    const ACTION_SERVER_PREFIX = '**';
    const ACTION_SERVER_CLASS = 'irc--14-99';

    // -----------------
    // ---- Parsing ----
    // -----------------

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] * @Kashike flails
    public static function parseAction(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= self::ACTION_USER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' ';

        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** @Kashike joined the channel
    public static function parseJoin(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' joined the channel';
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** @lol768 was kicked by @Kashike (hello)
    public static function parseKick(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getRecipientPrefix());
        $result .= self::transformActor($chat->getRecipientName());
        $result .= ' was kicked by ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= '(' . $chat->getMessage() . ')';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] <@lol768> meow!
    public static function parseMessage(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '&lt;';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::getSpanForColour(NickColours::getColourForNick(self::transformActor($chat->getActorName())), self::transformActor($chat->getActorName()));
        $result .= '&gt; ';

        // message
        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** Server sets mode +CQnst
    // [00:00:00] ** @Kashike sets mode +b *!*@test.com
    public static function parseMode(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';

        // mode set by internal actor
        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' sets mode ' . $chat->getMessage();
        } else {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
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

        }

        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    // [00:00:00] ** @drtshock is now known as @Trent
    public static function parseNick(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $prefix = self::createUserMode($chat->getActorPrefix());

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';

        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= $prefix;
        $result .= $chat->getActorName();
        $result .= ' is now known as ';
        $result .= $prefix;
        $result .= $chat->getRecipientName();
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parsePart(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' left the channel';
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parseQuit(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' ';
        $result .= 'has quit (' . $chat->getMessage() . ')';
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    public static function parseTopic(Chat $chat) {
        $result = '';
        $result .= self::provideTime($chat);

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';

        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= 'Topic is: ' . $chat->getMessage();
        } else {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' has changed the topic to: ' . $chat->getMessage();
        }

        $result .= '</span>';

        return $result;
    }

    // -----------------
    // ---- Helpers ----
    // -----------------

    /**
     * Provide the timestamp.
     *
     * @param Chat $chat
     * @return string
     */
    private static function provideTime(Chat $chat) {
        /** @var $date \DateTime */
        $date = $chat->getDate();
        return '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time
    }

    /**
     * @param $colour
     * @param $text
     * @return string
     */
    private static function getSpanForColour($colour, $text) {
        return '<span class="irc--' . $colour . '-99">' . $text . '</span>';
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