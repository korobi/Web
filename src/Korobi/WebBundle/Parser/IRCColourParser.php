<?php


namespace Korobi\WebBundle\Parser;


class IRCColourParser {

    public static function parseColour($messageFragment) {
        $regex = '/([0-9][0-9]?)(?:,([0-9][0-9]?))?/'; // mIRC accepts 0 => 99 and ignores invalid colours
        $matches = [];
        $retVal = preg_match($regex, $messageFragment, $matches);
        if ($retVal === 0) {
            return null;
        } else {
            $result = ['foreground' => sprintf('%02d', (int) $matches[1]), 'background' => 99];

            if (count($matches) > 2) {
                $result['background'] = sprintf("%02d", (int) $matches[2]);
            }
            return $result;
        }
    }

}
