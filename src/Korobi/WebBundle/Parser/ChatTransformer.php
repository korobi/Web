<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Controller\Generic\IRC\Channel\ChannelLogController;
use Korobi\WebBundle\Document\Chat;

class ChatTransformer {

    // todo - we don't put important constants in controllers!
    /**
     * @param Chat $chat
     * @return array
     */
    public static function transformMessage(Chat $chat) {
        $nick = LogParser::getDisplayName($chat);
        return [
            'timestamp'  => $chat->getDate()->getTimestamp(),
            'role'       => strtolower($chat->getActorPrefix()),
            'nickColour' => LogParser::getColourForActor($chat),
            'displayNick'=> substr($nick, 0, ChannelLogController::MAX_NICK_LENGTH + 1),
            'realNick'   => $nick,
            'nickTooLong'=> strlen($nick) - ChannelLogController::MAX_NICK_LENGTH > 1,
            'nick'       => LogParser::getActorName($chat),
            'message'    => LogParser::parseMessage($chat),
        ];
    }
}
