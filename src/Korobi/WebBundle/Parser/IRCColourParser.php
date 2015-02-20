<?php


namespace Korobi\WebBundle\Parser;


class IRCColourParser {

    public static function parseColour($messageFragment) {
        $regex = '/([0-9][0-9]?)(?:,([0-9][0-9]?))?/'; // mIRC accepts 0 => 99 and ignores invalid colours
        $matches = [];
        $retVal = preg_match($regex, $messageFragment, $matches, PREG_OFFSET_CAPTURE);
        if ($retVal === 0) {
            return null;
        } else {
            $result = ['foreground' => sprintf('%02d', (int) $matches[1][0]), 'background' => 99];
            $result['skip'] = $matches[1][1] + 1;

            if (count($matches) > 2) {
                $result['background'] = sprintf("%02d", (int) $matches[2][0]);
                $result['skip'] = $matches[2][1] + 1;
            }

            return $result;
        }
    }

}
