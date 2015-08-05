<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;

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

        $messages = [];
        if ($dbChannel->getLogsEnabled()) {
            $messages = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:Chat')
                ->findLastChatsByChannel($dbNetwork->getSlug(), $dbChannel->getChannel(), 5)
                ->toArray(false);
            $messages = array_reverse($messages);
        }

        $dbTopic = $dbChannel->getTopic();
        $topic = null;
        if ($dbTopic && !empty($dbTopic['value'])) { // TODO: Extract to RenderManager
            $topic = [
                'value' => $dbTopic['value'],
                'setter_nick' => $this->get("korobi.irc.log_parser")->transformActor($dbTopic['actor_nick']),
                'time' => $dbTopic['time']->toDateTime(),
            ];
        }


        $key = '';
        if($dbChannel->getKey() !== null && $this->authChecker->isGranted('ROLE_ADMIN')) {
            $key = '?key=' . $dbChannel->getKey();
        }


        // time to render!
        return $this->render('KorobiWebBundle:controller/generic/irc/channel:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'network_slug' => $dbNetwork->getSlug(),
            'channel_name' => $dbChannel->getChannel(),
            'channel' => $dbChannel,
            'topic' => $topic,
            'now' => time(),
            'sample_logs' => $this->getRenderManager()->renderLogs($messages),
            'slug' => self::transformChannelName($dbChannel->getChannel()),
            'command_prefix' => $dbChannel->getCommandPrefix(),
            'key' => $key,
        ]);
    }
}
