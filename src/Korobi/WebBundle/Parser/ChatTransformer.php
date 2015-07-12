<?php


namespace Korobi\WebBundle\Parser;


use Korobi\WebBundle\Controller\Generic\IRC\Channel\ChannelLogController;
use Korobi\WebBundle\Document\Chat;

class ChatTransformer {

    public static function transformMessage(Chat $chat) {
        $nick = LogParser::getDisplayName($chat);
        return [
            'timestamp'  => $chat->getDate()->getTimestamp(),
            'role'       => strtolower($chat->getActorPrefix()),
            'nickColour' => LogParser::getColourForActor($chat),
                                             // we don't put important constants in controllers!
            'displayNick'=> substr($nick, 0, ChannelLogController::MAX_NICK_LENGTH + 1),
            'realNick'   => $nick,
                                            // we don't put important constants in controllers!
            'nickTooLong'=> strlen($nick) - ChannelLogController::MAX_NICK_LENGTH > 1,
            'nick'       => LogParser::getActorName($chat),
            'message'    => LogParser::parseMessage($chat),
        ];
    }

}
