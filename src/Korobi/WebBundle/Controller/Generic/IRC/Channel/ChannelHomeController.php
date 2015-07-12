<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Korobi\WebBundle\Parser\LogParser;

class ChannelHomeController extends BaseController {

    /**
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction($network, $channel) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // create appropriate links
        $links = [];
        $linkBase = ['network' => $network, 'channel' => $channel];

        if ($dbChannel->getLogsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Logs', $this->generateUrl('channel_log', $linkBase));
        }

        if ($dbChannel->getCommandsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Commands', $this->generateUrl('channel_command', $linkBase));
        }

        $dbTopic = $dbChannel->getTopic();
        $topic = [
            'value' => $dbTopic['value'],
            'setter_nick' => LogParser::transformActor($dbTopic['actor_nick']),
            'time' => date('F j, Y h:i:s a', $dbTopic['time']->sec)
        ];

        $messages = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat')
            ->findLastChatsByChannel($dbNetwork->getSlug(), $dbChannel->getChannel(), 5)
            ->toArray(false);

        // time to render!
        return $this->render('KorobiWebBundle:controller/generic/irc/channel:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'network_slug' => $dbNetwork->getSlug(),
            'channel_name' => $dbChannel->getChannel(),
            'channel' => $dbChannel,
            'topic' => $topic,
            'now' => time(),
            'sample_logs' => $messages,
            'slug' => self::transformChannelName($dbChannel->getChannel()),
            'command_prefix' => $dbChannel->getCommandPrefix(),
            'links' => $links,
        ]);
    }

    private function createLink($dbChannel, $name, $href) {
        /** @var Channel $dbChannel */
        $result = [
            'name' => $name,
            'href' => $href,
        ];
        if($dbChannel->getKey() !== null && $this->authChecker->isGranted('ROLE_ADMIN')) {
            $result['href'] .= '?key=' . $dbChannel->getKey();
        }

        return $result;
    }
}
