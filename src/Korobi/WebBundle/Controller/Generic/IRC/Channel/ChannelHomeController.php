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

        // create appropriate links
        $links = [];
        $linkBase = ['network' => $network, 'channel' => $channel];

        if ($dbChannel->getLogsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Logs', $this->generateUrl('channel_log', $linkBase));
        }

        if ($dbChannel->getCommandsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Commands', $this->generateUrl('channel_command', $linkBase));
        }

        dump($dbChannel->getTopic());

        // time to render!
        return $this->render('KorobiWebBundle:controller/generic/irc/channel:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'channel' => $dbChannel,
            'now' => time(),
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
