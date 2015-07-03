<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Controller\Generic\IRC\Channel\ChannelLogController;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\LogParser;

class HomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->getRecentlyActiveChannels(5)
            ->toArray();
        $dbNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray(false);

        $messages = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat')
            ->findLastMessages(30)
            ->toArray(false);

        $networks = [];
        /** @var Network $dbNetwork */
        foreach($dbNetworks as $dbNetwork) {
            $networks[$dbNetwork->getSlug()] = $dbNetwork->getName();
        }

        return $this->render('KorobiWebBundle:controller/generic:home.html.twig', [
            'now' => time(),
            'channels' => $dbChannels,
            'networks' => $networks,
            'messages' => array_map([$this, 'transformMessage'], $messages),
        ]);
    }

    private function transformMessage(Chat $chat) {
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
