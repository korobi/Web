<?php


namespace Korobi\WebBundle\Parser;


class IRCColourParser {

    /**
     * @param string $messageFragment The fragment of message to extract the colours from.
     * @param bool $swap Swaps the foreground and background
     * @return array|null The array of data or null if no colour is present.
     */
    public static function parseColour($messageFragment, $swap=false, $defaultFg, $defaultBg) {
        $regex = '/([0-9][0-9]?)(?:,([0-9][0-9]?))?/'; // mIRC accepts 0 => 99 and ignores invalid colours
        $matches = [];
        $retVal = preg_match($regex, $messageFragment, $matches, PREG_OFFSET_CAPTURE);
        if ($retVal === 0) {
            return null;
        } else {
            $result = ['foreground' => sprintf('%02d', $matches[1][0]), 'background' => $defaultBg];
            $result['skip'] = $matches[1][1] + 1;
            if (strlen($matches[1][0]) === 1) {
                $result['skip'] = $result['skip'] - 1;
            }
            if (count($matches) > 2) {
                $result['background'] = sprintf("%02d", (int) $matches[2][0]);
                $result['skip'] = $matches[2][1] + 1;
                if (strlen($matches[2][0]) === 1) {
                    $result['skip'] = $result['skip'] - 1;
                }
            }
            if ($swap) {
                $result['foreground'] = $result['background'];
                $result['background'] = $result['foreground'];
                if (!count($matches) > 2) {
                    $result['background'] = $defaultFg;
                }


            }
            return $result;
        }
    }
}
