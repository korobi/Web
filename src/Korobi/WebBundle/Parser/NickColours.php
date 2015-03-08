<?php

namespace Korobi\WebBundle\Parser;

/**
 * @package Korobi\WebBundle\Parser
 */
class NickColours {
    // http://www.weechat.org/files/doc/stable/weechat_user.en.html
    // cyan,magenta,green,brown,lightblue,default,lightcyan,lightmagenta,lightgreen,blue
    public static $COLOUR_MAP = [10, 6, 3, 7, 12, IRCTextParser::DEFAULT_FOREGROUND, 11, 13, 9, 2];

    /**
     * @param string $nick The nick.
     * @return int The colour code for said nick.
     */
    public static function getColourForNick($nick) {
        // Made the decision to not support non-ASCII chars here
        // can revisit if this becomes an issue.
        $total = 0;
        foreach (str_split($nick) as $c) {
            $total += ord($c);
        }

        return self::$COLOUR_MAP[$total % count(self::$COLOUR_MAP)];
    }
}
