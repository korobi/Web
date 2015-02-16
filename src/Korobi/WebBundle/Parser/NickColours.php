<?php


namespace Korobi\WebBundle\Parser;


/**
 * Class NickColours
 * @package Korobi\WebBundle\Parser
 */
class NickColours {
    // cyan,magenta,green,brown,lightblue,default,lightcyan,lightmagenta,lightgreen,blue
    // http://www.weechat.org/files/doc/stable/weechat_user.en.html
    public static $COLOUR_MAP = ["10", "06", "03", "07", "12", "99", "11", "13", "09", "02"];

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