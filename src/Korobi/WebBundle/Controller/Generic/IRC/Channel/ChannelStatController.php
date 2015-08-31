<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Symfony\Component\HttpFoundation\Request;

class ChannelStatController extends BaseController {

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function statsAction(Request $request, $network, $channel) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        $statData = '';

        $statFilePath = $this->container->getParameter('channel_stats_root') . $dbNetwork->getSlug() . '/' . self::transformChannelName($dbChannel->getChannel()) . '.html';
        if(file_exists($statFilePath)) {
            $statFileContent = file_get_contents($statFilePath);
            foreach (explode('\n', $statFileContent) as $line) {
                $statData .= $line;//IRCTextParser::parseLine($line, true);
            }
        }

        return $this->render('KorobiWebBundle:controller/generic/irc/channel:stats.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'channel_private' => $dbChannel->isPrivate(),
            'data' => $statData,
        ]);
    }
}
