<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Symfony\Component\HttpFoundation\Request;

class ChannelStatsController extends BaseController {

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

        // check if this channel requires a key
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null || $key !== $dbChannel->getKey()) {
                throw new \Exception('Unauthorized'); // TODO
            }
        }

        // TODO
        $stats_file = file_get_contents($this->container->getParameter('channel_stats_root') . $dbNetwork->getSlug() . '/' . self::transformChannelName($dbChannel->getChannel()) . '.html');
        $stats_output = '';
        foreach (explode('\n', $stats_file) as $line) {
            $stats_output .= $line;//IRCTextParser::parseLine($line, true);
        }

        return $this->render('KorobiWebBundle:controller/channel:stats.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'data' => $stats_output,
        ]);
    }
}